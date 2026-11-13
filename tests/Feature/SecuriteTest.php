<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecuriteTest extends TestCase
{
    use RefreshDatabase;

    private function document(User $owner): Document
    {
        return Document::forceCreate([
            'user_id'           => $owner->id,
            'title'             => 'Facture',
            'original_filename' => 'facture.pdf',
            'file_path'         => 'documents/facture.pdf',
            'mime_type'         => 'application/pdf',
            'file_size'         => 1024,
            'page_count'        => 1,
            'processing_cost'   => 0,
            'status'            => 'completed',
        ]);
    }

    public function test_les_pages_privees_exigent_une_connexion(): void
    {
        foreach (['/dashboard', '/documents', '/invoices', '/invoices-statistics', '/profile', '/settings'] as $page) {
            $this->get($page)->assertRedirect(route('login'));
        }
    }

    public function test_un_utilisateur_ne_peut_pas_acceder_aux_documents_d_un_autre(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $document = $this->document($owner);

        $this->actingAs($other);

        $this->get("/documents/{$document->id}")->assertForbidden();
        $this->get("/documents/{$document->id}/download")->assertForbidden();
        $this->get("/documents/{$document->id}/ocr")->assertForbidden();
        $this->get("/documents/{$document->id}/api")->assertForbidden();
        $this->withoutMiddleware(ValidateCsrfToken::class)
            ->delete("/documents/{$document->id}")->assertForbidden();

        $this->assertDatabaseHas('documents', ['id' => $document->id]);
    }

    public function test_le_nombre_de_tentatives_de_connexion_est_limite(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'faux']);
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'faux'])->assertStatus(429);
    }
}
