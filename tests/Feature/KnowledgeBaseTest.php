<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeBaseTest extends TestCase
{
    use RefreshDatabase;

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
                'model'         => 'gemini-1.5-pro',
                'temperature'   => 0.7,
                'chunk_size'    => 800,
                'chunk_overlap' => 200,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Konfigurasi RAG berhasil disimpan.',
        ]);

        // Cek database
        $this->assertEquals('New custom system instructions.', Setting::getVal('rag_system_prompt'));
        $this->assertEquals('gemini-1.5-pro', Setting::getVal('rag_model'));
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
        // Buat dokumen aktif dengan content untuk dicocokkan
        Document::create([
            'title' => 'Panduan Magang Mandiri',
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
            'title' => 'Panduan Magang Mandiri',
        ]);

        // Tidak cocok
        $responseNoMatch = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.query'), [
                'query' => 'Jadwal wisuda tahun ini',
            ]);

        $responseNoMatch->assertStatus(200);
        $this->assertStringContainsString('Maaf, saya tidak menemukan informasi yang relevan', $responseNoMatch->json('answer'));
    }

    /**
     * Test public chatbot query (no authentication needed).
     */
    public function test_can_query_chatbot_publicly()
    {
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
}
