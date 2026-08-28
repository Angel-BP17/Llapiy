<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Area;
use App\Models\CampoType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DocumentsComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-documents', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'documents.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'documents.update', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'documents.delete', 'guard_name' => 'web']);
        
        $adminRole->givePermissionTo(Permission::all());

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    /**
     * 1. CONTRATO
     */
    public function test_document_index_returns_strict_inertia_contract()
    {
        Document::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)->get('/documentos');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('documents/index')
                ->has('documents')
                ->has('documentTypes')
                ->has('areas')
            );
    }

    /**
     * 2. VALIDACIÓN
     */
    public function test_document_creation_fails_with_invalid_data()
    {
        $response = $this->actingAs($this->adminUser)->post('/documentos', [
            'asunto' => '',
            'document_type_id' => 999
        ]);

        $response->assertSessionHasErrors(['asunto', 'document_type_id']);
    }

    /**
     * 3. SEGURIDAD
     */
    public function test_operator_cannot_delete_document()
    {
        $operator = User::factory()->create();
        $document = Document::factory()->create();

        $response = $this->actingAs($operator)->delete("/documentos/{$document->id}");

        $response->assertStatus(403);
    }

    /**
     * 4. RENDIMIENTO
     */
    public function test_document_index_is_optimized()
    {
        Document::factory()->count(10)->create();

        \DB::enableQueryLog();
        $this->actingAs($this->adminUser)->get('/documentos');
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        $this->assertLessThan(70, count($queries));
    }

    public function test_document_creation_validates_date_time_metadata()
    {
        $docType = DocumentType::factory()->create();

        $campoDate = CampoType::create(['name' => 'Fecha de Envío', 'data_type' => 'date']);
        $campoTime = CampoType::create(['name' => 'Hora de Envío', 'data_type' => 'time']);
        $campoDateTime = CampoType::create(['name' => 'Fecha y Hora', 'data_type' => 'date_time']);

        \DB::table('campo_document_types')->insert([
            ['document_type_id' => $docType->id, 'campo_type_id' => $campoDate->id, 'created_at' => now(), 'updated_at' => now()],
            ['document_type_id' => $docType->id, 'campo_type_id' => $campoTime->id, 'created_at' => now(), 'updated_at' => now()],
            ['document_type_id' => $docType->id, 'campo_type_id' => $campoDateTime->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 1. Caso inválido
        $responseInvalid = $this->actingAs($this->adminUser)->post('/documentos', [
            'asunto' => 'Documento Invalido',
            'n_documento' => 'D-001',
            'folios' => '5',
            'fecha' => '2026-07-09',
            'document_type_id' => $docType->id,
            'campos' => [
                ['id' => $campoDate->id, 'dato' => 'not-a-date'],
                ['id' => $campoTime->id, 'dato' => 'not-a-time'],
                ['id' => $campoDateTime->id, 'dato' => 'not-a-datetime'],
            ],
        ]);

        $responseInvalid->assertSessionHasErrors([
            'campos.0.dato',
            'campos.1.dato',
            'campos.2.dato',
        ]);

        // 2. Caso válido
        $responseValid = $this->actingAs($this->adminUser)->post('/documentos', [
            'asunto' => 'Documento Valido',
            'n_documento' => 'D-002',
            'folios' => '5',
            'fecha' => '2026-07-09',
            'document_type_id' => $docType->id,
            'campos' => [
                ['id' => $campoDate->id, 'dato' => '2026-07-09'],
                ['id' => $campoTime->id, 'dato' => '17:15:00'],
                ['id' => $campoDateTime->id, 'dato' => '2026-07-09 17:15:00'],
            ],
        ]);

        $responseValid->assertRedirect();
        $this->assertDatabaseHas('documents', ['asunto' => 'Documento Valido']);
    }
}
