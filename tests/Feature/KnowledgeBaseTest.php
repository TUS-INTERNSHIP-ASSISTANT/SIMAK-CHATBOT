<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KnowledgeBaseTest extends TestCase
{
    use RefreshDatabase;

    private const DOC_TITLE_MAGANG = 'Panduan Magang Mandiri';
    private const GROQ_ANSWER_PREFIX = 'Jawaban Groq:';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->user = User::factory()->create([
            'role' => 'staff',
        ]);
    }

    /**
     * Test index access requires authentication.
     */
    public function test_access_requires_login()
    {
        $response = $this->get('/dashboard/knowledge-base');
        $response->assertRedirect(route('login'));
    }

    /**
     * Test index loads statistics and config.
     */
    public function test_can_view_knowledge_base_dashboard()
    {
        // Buat beberapa dokumen aktif
        Document::create([
            'title' => 'Panduan Magang',
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'chunk_count' => 12,
            'indexed_at' => now(),
        ]);

        Document::create([
            'title' => 'Panduan Kerja Praktik',
            'type' => 'docx',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'chunk_count' => 8,
            'indexed_at' => now()->subDay(),
        ]);

        // Dokumen nonaktif (seharusnya di-exclude dari RAG dataset)
        Document::create([
            'title' => 'Dokumen Lama',
            'type' => 'pdf',
            'status' => 'inactive',
            'uploaded_by' => $this->user->id,
            'chunk_count' => 0,
        ]);

        $response = $this->actingAs($this->user, 'web')->get('/dashboard/knowledge-base');

        $response->assertStatus(200);

        // Cek apakah data di-pass ke view
        $response->assertViewHas('activeDocsCount', 2);
        $response->assertViewHas('totalChunks', 20);
        $response->assertSee('Panduan Magang');
        $response->assertSee('Panduan Kerja Praktik');
        $response->assertDontSee('Dokumen Lama');
    }

    /**
     * Test saving RAG settings.
     */
    public function test_can_save_rag_settings()
    {
        $response = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.settings'), [
                'system_prompt' => 'New custom system instructions.',
                'knowledge_base_prompt' => 'Knowledge base tuning instructions.',
                'model' => 'groq-llama3-8b',
                'temperature' => 0.7,
                'chunk_size' => 800,
                'chunk_overlap' => 200,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Konfigurasi RAG berhasil disimpan.',
        ]);

        // Cek database
        $this->assertEquals('New custom system instructions.', Setting::getVal('rag_system_prompt'));
        $this->assertEquals('Knowledge base tuning instructions.', Setting::getVal('rag_knowledge_base_prompt'));
        $this->assertEquals('groq-llama3-8b', Setting::getVal('rag_model'));
        $this->assertEquals(0.7, (float) Setting::getVal('rag_temperature'));
        $this->assertEquals(800, (int) Setting::getVal('rag_chunk_size'));
        $this->assertEquals(200, (int) Setting::getVal('rag_chunk_overlap'));
    }

    /**
     * Test ingestion sync.
     */
    public function test_can_trigger_sync()
    {
        // Buat dokumen aktif tanpa chunk_count dan indexed_at
        $doc = Document::create([
            'title' => 'SOP KP',
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'file_size' => 2048, // 2 KB
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.sync'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $doc->refresh();
        $this->assertNotNull($doc->indexed_at);
        $this->assertGreaterThan(0, $doc->chunk_count);
        $this->assertNotEmpty($doc->content);
    }

    /**
     * Test playground query.
     */
    public function test_can_query_playground()
    {

        config()->set('services.groq.api_key', '');
        config()->set('services.openai.api_key', '');

        // Buat dokumen aktif dengan content untuk dicocokkan
        Document::create([
            'title' => self::DOC_TITLE_MAGANG,
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'content' => 'Prosedur pendaftaran magang online harus melalui portal SIMAK.',
        ]);

        // Cocok dengan keyword "magang"
        $response = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.query'), [
                'query' => 'Bagaimana cara daftar magang?',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'title' => self::DOC_TITLE_MAGANG,
        ]);

        // Tidak cocok – pastikan fallback lokal digunakan
        $responseNoMatch = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.query'), [
                'query' => 'Jadwal wisuda tahun ini',
            ]);

        $responseNoMatch->assertStatus(200);
        $this->assertStringContainsString('tidak menemukan', $responseNoMatch->json('answer'));
    }

    /**
     * Test public chatbot query (no authentication needed).
     */
    public function test_can_query_chatbot_publicly()
    {
        // Null out API key agar fallback lokal digunakan (tidak memanggil API eksternal)
        config()->set('services.groq.api_key', '');
        config()->set('services.openai.api_key', '');

        // Buat dokumen aktif dengan content untuk dicocokkan
        Document::create([
            'title' => 'SOP Kerja Praktik Elektro',
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'content' => 'Syarat mengajukan Kerja Praktik adalah lulus minimal 90 SKS.',
        ]);

        // Kirim kueri tanpa login/actingAs
        $response = $this->postJson(route('chatbot.query'), [
            'query' => 'syarat kerja praktik',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'title' => 'SOP Kerja Praktik Elektro',
        ]);
        $this->assertStringContainsString('lulus **90 SKS**', $response->json('answer'));
    }

    /**
     * Test query menggunakan Groq API saat model Groq dipilih.
     */
    public function test_query_uses_groq_api_when_configured()
    {
        Setting::setVal('rag_model', 'groq-llama3-8b');
        Setting::setVal('rag_temperature', 0.3);
        Setting::setVal('rag_system_prompt', 'Anda asisten SIMAK.');

        Document::create([
            'title' => self::DOC_TITLE_MAGANG,
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'content' => 'Pendaftaran magang dilakukan melalui portal SIMAK dengan melampirkan CV dan transkrip.',
        ]);

        config()->set('services.groq.api_key', 'test-groq-key');

        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Jawaban Groq: Pendaftaran magang dilakukan melalui portal SIMAK dengan CV dan transkrip.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.query'), [
                'query' => 'Bagaimana cara daftar magang?',
            ]);

        $response->assertStatus(200);
        $this->assertStringContainsString(self::GROQ_ANSWER_PREFIX, $response->json('answer'));
        $response->assertJsonFragment([
            'title' => self::DOC_TITLE_MAGANG,
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/chat/completions')
                && data_get($request->data(), 'model') === 'llama-3.1-8b-instant'
                && str_contains(data_get($request->data(), 'messages.0.content', ''), 'Aturan tambahan');
        });
    }

    /**
     * Test retrieval memilih chunk yang spesifik pada dokumen panjang.
     */
    public function test_query_uses_specific_chunk_from_long_document()
    {
        Setting::setVal('rag_model', 'groq-llama3-8b');
        Setting::setVal('rag_temperature', 0.3);
        Setting::setVal('rag_chunk_size', 180);
        Setting::setVal('rag_chunk_overlap', 0);

        $longContent = str_repeat('Magang adalah program pembelajaran di industri. ', 12)
            . 'Bagian penting berikutnya: konversi SKS dapat dipertimbangkan jika ada ketentuan resmi dari prodi. '
            . str_repeat('Informasi tambahan tidak relevan. ', 8);

        Document::create([
            'title' => 'Panduan Magang Panjang',
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'content' => $longContent,
        ]);

        config()->set('services.groq.api_key', 'test-groq-key');

        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Jawaban Groq: konversi SKS bisa dipertimbangkan sesuai ketentuan prodi.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.query'), [
                'query' => 'apakah magang bisa konversi sks?',
            ]);

        $response->assertStatus(200);
        $this->assertStringContainsString(self::GROQ_ANSWER_PREFIX, $response->json('answer'));

        Http::assertSent(function ($request) {
            $userMessage = data_get($request->data(), 'messages.1.content', '');

            return str_contains($request->url(), '/chat/completions')
                && str_contains($userMessage, 'konversi SKS dapat dipertimbangkan');
        });
    }

    /**
     * Test Groq tetap dipanggil meski tidak ada dokumen aktif, selama prompt knowledge base tersedia.
     */
    public function test_query_uses_knowledge_base_prompt_even_without_documents()
    {
        Setting::setVal('rag_model', 'groq-llama3-8b');
        Setting::setVal('rag_knowledge_base_prompt', 'SIMAK knowledge base prompt for domain guidance.');

        config()->set('services.groq.api_key', 'test-groq-key');

        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Jawaban Groq: gunakan pedoman knowledge base SIMAK.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.query'), [
                'query' => 'apa itu simak?',
            ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('Jawaban Groq:', $response->json('answer'));

        Http::assertSent(function ($request) {
            return str_contains(data_get($request->data(), 'messages.0.content', ''), 'SIMAK knowledge base prompt for domain guidance.')
                && str_contains(data_get($request->data(), 'messages.1.content', ''), 'Pertanyaan pengguna: apa itu simak?');
        });
    }

    /**
     * Test query menggunakan singkatan "kp" tetap dapat menemukan dokumen kerja praktik.
     */
    public function test_query_with_kp_abbreviation_can_match_kerja_praktik_content()
    {
        Document::create([
            'title' => 'Pedoman Kerja Praktik',
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'content' => 'Periode pendaftaran kerja praktik dibuka setiap awal semester ganjil melalui sistem SIMAK.',
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.query'), [
                'query' => 'kapan periode pendaftaran kp?',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Pedoman Kerja Praktik',
        ]);

        $this->assertStringNotContainsString(
            'Maaf, saya tidak menemukan informasi yang relevan',
            (string) $response->json('answer')
        );
    }

    /**
     * Test query menggunakan OpenAI API saat model OpenAI dipilih.
     */
    public function test_query_uses_openai_api_when_configured()
    {
        Setting::setVal('rag_model', 'openai-gpt-4o-mini');
        Setting::setVal('rag_temperature', 0.3);
        Setting::setVal('rag_system_prompt', 'Anda adalah SIMAK, Asisten Virtual SSC.');

        Document::create([
            'title' => self::DOC_TITLE_MAGANG,
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'content' => 'Pendaftaran magang dilakukan melalui portal SIMAK dengan melampirkan CV dan transkrip.',
        ]);

        config()->set('services.openai.api_key', 'test-openai-key');

        Http::fake([
            'https://api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Jawaban OpenAI: Pendaftaran magang dilakukan melalui portal SIMAK dengan CV dan transkrip.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.query'), [
                'query' => 'Bagaimana cara daftar magang?',
            ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('Jawaban OpenAI:', $response->json('answer'));
        $response->assertJsonFragment([
            'title' => self::DOC_TITLE_MAGANG,
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/chat/completions')
                && data_get($request->data(), 'model') === 'gpt-4o-mini';
        });
    }

    /**
     * Test sync memperbarui kb_last_updated_at di settings.
     */
    public function test_sync_updates_kb_last_updated_timestamp()
    {
        Document::create([
            'title' => 'Panduan KP',
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.sync'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['kb_last_updated']);

        // Pastikan timestamp disimpan ke settings
        $this->assertNotNull(Setting::getVal('kb_last_updated_at'));
    }
}
