<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\DocumentTypes\CampoController;
use App\Http\Controllers\DocumentTypes\DocumentTypeController;
use App\Models\CampoType;
use App\Models\DocumentType;
use App\Services\DocumentTypes\CampoService;
use App\Services\DocumentTypes\DocumentTypeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class DocumentTypesControllersTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_type_index_returns_json_with_data(): void
    {
        $service = Mockery::mock(DocumentTypeService::class);
        $service->shouldReceive('getIndexData')->once()->andReturn([
            'documentTypes' => DocumentType::query()->paginate(10),
            'areas' => collect(),
        ]);

        $controller = new DocumentTypeController($service);
        $response = $controller->index(Mockery::mock(\App\Http\Requests\DocumentType\IndexDocumentTypeRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_document_type_store_returns_json_on_success(): void
    {
        $service = Mockery::mock(DocumentTypeService::class);
        $request = Mockery::mock(\App\Http\Requests\DocumentType\CreateDocumentTypeRequest::class);
        $request->shouldReceive('input')->once()->with('name')->andReturn('Tipo API');
        $service->shouldReceive('create')->once()->with($request);

        $controller = new DocumentTypeController($service);
        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(201, $response->status());
    }

    public function test_document_type_update_returns_json_on_success(): void
    {
        $service = Mockery::mock(DocumentTypeService::class);
        $request = Mockery::mock(\App\Http\Requests\DocumentType\UpdateDocumentTypeRequest::class);
        $documentType = DocumentType::create(['name' => 'Tipo A']);

        $service->shouldReceive('update')->once()->with($request, $documentType);

        $controller = new DocumentTypeController($service);
        $response = $controller->update($request, $documentType);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_document_type_destroy_returns_json_on_success(): void
    {
        $service = Mockery::mock(DocumentTypeService::class);
        $documentType = DocumentType::create(['name' => 'Tipo B']);

        $service->shouldReceive('delete')->once()->with($documentType);

        $controller = new DocumentTypeController($service);
        $response = $controller->destroy($documentType);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_campo_index_returns_json_with_data(): void
    {
        $service = Mockery::mock(CampoService::class);
        $service->shouldReceive('getIndexData')->once()->andReturn([
            'campos' => CampoType::query()->paginate(10),
        ]);

        $controller = new CampoController($service);
        $response = $controller->index(Mockery::mock(\App\Http\Requests\Campo\IndexCampoRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_campo_store_validates_and_returns_json(): void
    {
        $service = Mockery::mock(CampoService::class);
        $payload = ['name' => 'Campo Uno', 'data_type' => 'string'];
        $service->shouldReceive('create')->once()->with($payload);

        $controller = new CampoController($service);
        $request = Mockery::mock(\App\Http\Requests\Campo\StoreCampoRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($payload);
        $request->shouldReceive('input')->once()->with('name')->andReturn('Campo Uno');
        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(201, $response->status());
    }

    public function test_campo_update_validates_and_returns_json(): void
    {
        $service = Mockery::mock(CampoService::class);
        $campo = CampoType::create(['name' => 'Campo Base']);
        $payload = ['name' => 'Campo Editado', 'data_type' => 'string'];
        $request = Mockery::mock(\App\Http\Requests\Campo\UpdateCampoRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($payload);

        $service->shouldReceive('update')->once()->with($campo, $payload);

        $controller = new CampoController($service);
        $response = $controller->update($request, $campo);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_campo_destroy_returns_json(): void
    {
        $service = Mockery::mock(CampoService::class);
        $campo = CampoType::create(['name' => 'Campo Eliminar']);

        $service->shouldReceive('delete')->once()->with($campo);

        $controller = new CampoController($service);
        $response = $controller->destroy($campo);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }
}
