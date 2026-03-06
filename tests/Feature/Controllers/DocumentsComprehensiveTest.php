<?php

namespace Tests\Feature\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\Group;
use App\Models\Subgroup;
use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\GroupType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentsComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $operatorUser;
    protected $documentType;

    protected function setUp(): void
    {
        parent::setUp();

        // Configuración Base de Roles y Permisos (RBAC)
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        $operatorRole = Role::firstOrCreate(['name' => 'OPERADOR', 'guard_name' => 'web']);
        
        Permission::firstOrCreate(['name' => 'documents.delete', 'guard_name' => 'web']);
        $adminRole->givePermissionTo('documents.delete');

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);

        $this->operatorUser = User::factory()->create();
        $this->operatorUser->assignRole($operatorRole);

        $this->documentType = DocumentType::factory()->create(['name' => 'Resolución']);
    }

    /** 
     * 1. Etapa de Contrato (API Schema) 
     * Garantizar que la estructura del JSON nunca cambie de forma imprevista.
     */
    public function test_document_index_returns_strict_api_contract_schema()
    {
        Document::factory()->count(3)->create(['document_type_id' => $this->documentType->id]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/documents');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'documents' => [
                        'current_page',
                        'data' => [
                            '*' => [
                                'id', 'n_documento', 'asunto', 'folios', 'root', 'fecha', 'periodo', 
                                'user_id', 'document_type_id', 'group_id', 'subgroup_id', 'created_at',
                                'document_type', 'user', 'campos'
                            ]
                        ],
                        'total',
                        'per_page'
                    ],
                    'areas', 'groups', 'subgroups', 'documentTypes', 'years'
                ]
            ]);
    }

    /** 
     * 2. Etapa de Validación (Gatekeeping) 
     * Enviar tipos de datos erróneos y verificar que no lleguen a SQL.
     */
    public function test_document_creation_fails_with_invalid_data_types_and_missing_fields()
    {
        $response = $this->actingAs($this->adminUser)->postJson('/api/documents', [
            'n_documento' => str_repeat('A', 300), // Demasiado largo (max:255)
            'asunto' => '', // Vacío
            'folios' => str_repeat('1', 60), // Demasiado largo (max:50)
            'fecha' => 'Formato Invalido',
            'document_type_id' => 'Letras', 
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['n_documento', 'asunto', 'folios', 'fecha', 'document_type_id']);
    }

    /** 
     * 3. Etapa de Seguridad y Roles (RBAC) 
     * Verificar que un OPERADOR no pueda eliminar documentos.
     */
    public function test_operator_cannot_delete_document()
    {
        $document = Document::factory()->create();

        $response = $this->actingAs($this->operatorUser)->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(403);
    }

    /** 
     * 4. Etapa de Integridad de Modelos (Relaciones)
     * Validar que no se puedan asociar documentos a tipos que no existen.
     */
    public function test_document_cannot_be_created_with_non_existent_relations()
    {
        $response = $this->actingAs($this->adminUser)->postJson('/api/documents', [
            'n_documento' => 'DOC-999',
            'asunto' => 'Prueba Integridad',
            'folios' => 10,
            'fecha' => '2023-01-01',
            'document_type_id' => 99999, // ID Inexistente
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['document_type_id']);
    }

    /** 
     * 5. Etapa de Reglas de Negocio (Service Layer)
     * Validar que el conteo y agrupamiento de estadísticas (ej. Dashboard o Scope) funcione.
     */
    public function test_document_types_counter_respects_area_scope_business_logic()
    {
        // Esta prueba valida el resolveDocumentTypesCounter en el controlador
        $area = Area::factory()->create();
        $groupType = GroupType::factory()->create();
        $areaGroupType = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $groupType->id]);
        $group = Group::factory()->create(['area_group_type_id' => $areaGroupType->id]);
        
        $this->documentType->groups()->attach($group->id);

        $response = $this->actingAs($this->adminUser)->getJson("/api/documents?document_type_scope=area:{$area->id}");

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Debe existir al menos 1 tipo de documento para esta área
        $this->assertGreaterThanOrEqual(1, $data['documentTypesCount']);
        $this->assertStringContainsString("Tipos de documento del area:", $data['documentTypesCountLabel']);
    }

    /** 
     * 6. Etapa de Manejo de Archivos (Storage)
     * Validar subida correcta, tipos de archivo y descarga/streaming seguro.
     */
    public function test_document_file_upload_and_secure_streaming()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

        $document = Document::factory()->create(['user_id' => $this->adminUser->id]);

        // Upload
        $uploadResponse = $this->actingAs($this->adminUser)->putJson("/api/documents/{$document->id}/upload", [
            'root' => $file
        ]);

        $uploadResponse->assertStatus(200);
        $this->assertNotNull($uploadResponse->json('data.document.root'));

        // View File / Streaming
        $viewResponse = $this->actingAs($this->adminUser)->getJson("/api/documents/{$document->id}/file");
        $viewResponse->assertStatus(200)
                     ->assertHeader('Content-Type', 'application/pdf');
    }

    /** 
     * 7. Etapa de Resiliencia (Edge Cases)
     * Soportar emojis, caracteres especiales y peticiones paginadas grandes.
     */
    public function test_api_is_resilient_to_special_characters_and_emojis()
    {
        $weirdAsunto = "Resolución con tildes, ñandú y emojis 🔥🚀 (2024)";
        
        Document::factory()->create([
            'asunto' => $weirdAsunto,
            'document_type_id' => $this->documentType->id
        ]);

        $response = $this->actingAs($this->adminUser)->getJson("/api/documents?asunto=" . urlencode("ñandú y emojis 🔥"));
        
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.documents.data'));
        $this->assertEquals($weirdAsunto, $response->json('data.documents.data.0.asunto'));
    }

    /** 
     * 8. Etapa de Rendimiento (N+1 Queries)
     * Validar que el listado de documentos no ejecute consultas exponenciales.
     */
    public function test_document_index_is_optimized_against_n_plus_one_query_problems()
    {
        // Crear 10 documentos
        Document::factory()->count(10)->create(['document_type_id' => $this->documentType->id]);

        DB::enableQueryLog();

        $this->actingAs($this->adminUser)->getJson('/api/documents');

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // En un listado optimizado con Eager Loading, 10 registros no deberían requerir muchas consultas (auth, index, counts, relations).
        // Sin embargo, las comprobaciones de RBAC y las relaciones complejas pueden llegar a ~25 consultas.
        // Si hay N+1 real (consultar por cada registro), el número sobrepasará las 40.
        $this->assertLessThan(35, count($queries), "Se detectó un posible problema de N+1 Queries. Se ejecutaron " . count($queries) . " consultas.");
    }
}
