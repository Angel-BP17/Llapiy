<?php

namespace Tests\Feature\Services;

use App\Models\CampoType;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use App\Services\Document\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected $documentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->documentService = new DocumentService();
    }

    /** @test */
    public function it_caches_available_years_in_documents_list()
    {
        Document::factory()->create(['fecha' => '2023-01-01']);
        
        // Primera llamada: Debe ejecutar la consulta y guardar en cache
        $data1 = $this->documentService->getAll((object)['asunto' => null, 'document_type_id' => null, 'area_id' => null, 'group_id' => null, 'subgroup_id' => null, 'role_id' => null, 'year' => null, 'month' => null]);
        
        $this->assertTrue(Cache::has('documents_available_years'));
        $this->assertEquals([2023], $data1['years']->toArray());

        // Segunda llamada: Debe usar el cache
        Document::factory()->create(['fecha' => '2024-01-01']); // Creamos otro pero no debería aparecer si el cache funciona
        $data2 = $this->documentService->getAll((object)['asunto' => null, 'document_type_id' => null, 'area_id' => null, 'group_id' => null, 'subgroup_id' => null, 'role_id' => null, 'year' => null, 'month' => null]);
        
        $this->assertEquals([2023], $data2['years']->toArray());
    }

    /** @test */
    public function it_uses_batch_insert_for_document_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $docType = DocumentType::factory()->create();
        $campoTypes = CampoType::factory()->count(3)->create();
        
        $data = [
            'n_documento' => 'DOC-001',
            'asunto' => 'Test Batch Insert',
            'folios' => 10,
            'document_type_id' => $docType->id,
            'fecha' => '2023-05-05',
        ];

        $inputs = $campoTypes->map(fn($ct) => [
            'id' => $ct->id,
            'dato' => 'Valor ' . $ct->id
        ])->toArray();

        // Contar consultas durante la creación
        DB::enableQueryLog();
        $document = $this->documentService->create($data, null, $inputs);
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertDatabaseHas('documents', ['asunto' => 'Test Batch Insert']);
        $this->assertCount(3, $document->campos);
        
        // Debería haber una sola consulta de INSERT para la tabla campos (batch insert)
        $campoInserts = collect($queries)->filter(fn($q) => str_contains($q['query'], 'insert into `campos`'))->count();
        $this->assertEquals(1, $campoInserts, "Se esperaba solo 1 consulta de inserción para múltiples campos.");
    }
}
