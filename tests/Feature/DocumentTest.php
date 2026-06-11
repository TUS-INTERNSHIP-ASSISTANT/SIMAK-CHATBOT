<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutVite();

        // Buat user untuk login
        $this->user = User::factory()->create([
            'role' => 'staff',
        ]);
        
        Storage::fake('local');
    }

    /**
     * Test index page access requires authentication.
     */
    public function test_access_requires_login()
    {
        $response = $this->get('/dashboard/kelola-dokumen');
        $response->assertRedirect(route('login'));
    }

    /**
     * Test index page displays documents.
     */
    public function test_can_view_document_list()
    {
        $doc = Document::create([
            'title' => 'Panduan Mahasiswa',
            'description' => 'SOP Panduan Mahasiswa Baru',
            'type' => 'pdf',
            'file_path' => 'documents/panduan.pdf',
            'original_filename' => 'panduan.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'web')->get('/dashboard/kelola-dokumen');
        
        $response->assertStatus(200);
        $response->assertSee('Panduan Mahasiswa');
    }

    /**
     * Test file type validation (mismatch).
     */
    public function test_cannot_upload_mismatched_file_type()
    {
        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        // Pilih type docx tapi upload pdf
        $response = $this->actingAs($this->user, 'web')->post('/dashboard/kelola-dokumen', [
            'title' => 'Mismatched Doc',
            'type' => 'docx',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertEquals(0, Document::count());
    }

    /**
     * Test successful document upload.
     */
    public function test_can_upload_document()
    {
        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user, 'web')->post('/dashboard/kelola-dokumen', [
            'title' => 'Valid PDF Doc',
            'description' => 'Test description',
            'type' => 'pdf',
            'file' => $file,
        ]);

        $response->assertRedirect(route('dashboard.kelola-dokumen.index'));
        $response->assertSessionHas('success');
        
        $this->assertEquals(1, Document::count());
        $doc = Document::first();
        $this->assertEquals('Valid PDF Doc', $doc->title);
        $this->assertEquals('pdf', $doc->type);
        Storage::disk('local')->assertExists($doc->file_path);
    }

    /**
     * Test download file.
     */
    public function test_can_download_document()
    {
        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');
        
        $responseUpload = $this->actingAs($this->user, 'web')->post('/dashboard/kelola-dokumen', [
            'title' => 'Download Doc',
            'type' => 'pdf',
            'file' => $file,
        ]);

        $doc = Document::first();
        
        $responseDownload = $this->actingAs($this->user, 'web')
            ->get(route('dashboard.kelola-dokumen.show', $doc));

        $responseDownload->assertStatus(200);
        $responseDownload->assertHeader('content-disposition', 'attachment; filename=document.pdf');
    }

    /**
     * Test edit/update document.
     */
    public function test_can_update_document()
    {
        $doc = Document::create([
            'title' => 'Old Title',
            'description' => 'Old Desc',
            'type' => 'pdf',
            'file_path' => 'documents/test.pdf',
            'original_filename' => 'test.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->put(route('dashboard.kelola-dokumen.update', $doc), [
                'title' => 'New Title',
                'description' => 'New Desc',
                'status' => 'inactive',
            ]);

        $response->assertRedirect(route('dashboard.kelola-dokumen.index'));
        $response->assertSessionHas('success');

        $doc->refresh();
        $this->assertEquals('New Title', $doc->title);
        $this->assertEquals('New Desc', $doc->description);
        $this->assertEquals('inactive', $doc->status);
    }

    /**
     * Test soft deletion.
     */
    public function test_can_soft_delete_document()
    {
        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');
        
        // Simpan file fisik
        $path = Storage::disk('local')->putFile('documents', $file);

        $doc = Document::create([
            'title' => 'To Delete',
            'type' => 'pdf',
            'file_path' => $path,
            'original_filename' => 'document.pdf',
            'file_size' => 500,
            'mime_type' => 'application/pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
        ]);

        Storage::disk('local')->assertExists($path);

        $response = $this->actingAs($this->user, 'web')
            ->delete(route('dashboard.kelola-dokumen.destroy', $doc));

        $response->assertRedirect(route('dashboard.kelola-dokumen.index'));
        $response->assertSessionHas('success');

        // Check soft deleted
        $this->assertEquals(0, Document::count());
        $this->assertEquals(1, Document::withTrashed()->count());
        
        // Check physical file deleted
        Storage::disk('local')->assertMissing($path);
    }

    /**
     * Test restore soft deleted document.
     */
    public function test_can_restore_document()
    {
        $doc = Document::create([
            'title' => 'To Restore',
            'type' => 'pdf',
            'file_path' => 'documents/test.pdf',
            'status' => 'active',
            'uploaded_by' => $this->user->id,
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'web')
            ->post(route('dashboard.kelola-dokumen.restore', $doc->id));

        $response->assertRedirect(route('dashboard.kelola-dokumen.index'));
        $response->assertSessionHas('success');

        $doc->refresh();
        $this->assertNull($doc->deleted_at);
        $this->assertEquals(1, Document::count());
    }
}
