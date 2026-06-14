<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Setting;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->user = User::factory()->create([
            'name' => 'Test Staff',
            'role' => 'staff',
        ]);
    }

    /**
     * Test retrieving activity logs endpoint.
     */
    public function test_can_fetch_activity_logs_endpoint()
    {
        ActivityLog::create([
            'user_id' => $this->user->id,
            'activity' => 'Test action',
            'type' => 'update',
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->getJson(route('dashboard.activity-logs'));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'activity' => 'Test action',
            'type' => 'update',
        ]);
    }

    /**
     * Test logging on web login.
     */
    public function test_logs_activity_on_web_login()
    {
        $response = $this->postJson(route('login.post'), [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'login',
            'activity' => "Staff {$this->user->name} berhasil login",
        ]);
    }

    /**
     * Test logging on web logout.
     */
    public function test_logs_activity_on_web_logout()
    {
        $response = $this->actingAs($this->user, 'web')
            ->post(route('logout'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'logout',
            'activity' => "Staff {$this->user->name} logout",
        ]);
    }

    /**
     * Test logging on document creation.
     */
    public function test_logs_activity_on_document_upload()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('panduan.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user, 'web')
            ->post(route('dashboard.kelola-dokumen.store'), [
                'title' => 'Panduan Magang Baru',
                'description' => 'Deskripsi panduan',
                'type' => 'pdf',
                'file' => $file,
            ]);

        $response->assertRedirect(route('dashboard.kelola-dokumen.index'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'upload',
            'activity' => "Staff {$this->user->name} mengunggah file panduan.pdf",
        ]);
    }

    /**
     * Test logging on document update.
     */
    public function test_logs_activity_on_document_update()
    {
        $document = Document::create([
            'title' => 'SOP KP',
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->put(route('dashboard.kelola-dokumen.update', $document->id), [
                'title' => 'SOP KP Baru',
                'description' => 'Updated desc',
                'status' => 'inactive',
            ]);

        $response->assertRedirect(route('dashboard.kelola-dokumen.index'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'update',
            'activity' => "Staff {$this->user->name} memperbarui dokumen SOP KP Baru",
        ]);
    }

    /**
     * Test logging on document delete.
     */
    public function test_logs_activity_on_document_delete()
    {
        $document = Document::create([
            'title' => 'SOP KP',
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->delete(route('dashboard.kelola-dokumen.destroy', $document->id));

        $response->assertRedirect(route('dashboard.kelola-dokumen.index'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'delete',
            'activity' => "Staff {$this->user->name} menghapus dokumen SOP KP",
        ]);
    }

    /**
     * Test logging on document restore.
     */
    public function test_logs_activity_on_document_restore()
    {
        $document = Document::create([
            'title' => 'SOP KP',
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
        ]);
        $document->delete(); // Soft delete

        $response = $this->actingAs($this->user, 'web')
            ->post(route('dashboard.kelola-dokumen.restore', $document->id));

        $response->assertRedirect(route('dashboard.kelola-dokumen.index'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'update',
            'activity' => "Staff {$this->user->name} memulihkan dokumen SOP KP",
        ]);
    }

    /**
     * Test logging on RAG settings save.
     */
    public function test_logs_activity_on_rag_settings_save()
    {
        $response = $this->actingAs($this->user, 'web')
            ->post(route('dashboard.knowledge-base.settings'), [
                'system_prompt' => 'New prompt text',
                'knowledge_base_prompt' => 'New kb text',
                'model' => 'gemini-1.5-flash',
                'temperature' => 0.5,
                'chunk_size' => 750,
                'chunk_overlap' => 150,
            ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'update',
            'activity' => "Staff {$this->user->name} memperbarui konfigurasi RAG",
        ]);
    }

    /**
     * Test logging on RAG sync.
     */
    public function test_logs_activity_on_rag_sync()
    {
        Document::create([
            'title' => 'SOP KP',
            'type' => 'pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->postJson(route('dashboard.knowledge-base.sync'));

        $response->assertStatus(200);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'sync',
            'activity' => "Staff {$this->user->name} menyinkronkan basis pengetahuan",
        ]);
    }
}
