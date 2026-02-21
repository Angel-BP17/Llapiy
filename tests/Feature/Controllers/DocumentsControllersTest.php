<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Documents\BlockController;
use App\Http\Controllers\Documents\DocumentController;
use App\Http\Controllers\Inbox\InboxController;
use App\Http\Controllers\Inbox\NotificationController;
use App\Models\Block;
use App\Models\Document;
use App\Models\User;
use App\Services\ActivityLog\ActivityLogService;
use App\Services\Block\BlockService;
use App\Services\Document\DocumentService;
use App\Services\Inbox\InboxService;
use App\Services\Inbox\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class DocumentsControllersTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_index_returns_json(): void
    {
        $user = $this->createUser();
        Auth::login($user);

        $service = Mockery::mock(DocumentService::class);
        $service->shouldReceive('getAll')->once()->andReturn([
            'documents' => Document::query(),
            'areas' => collect(),
            'groups' => collect(),
            'subgroups' => collect(),
            'documentTypes' => collect(),
            'years' => [],
        ]);
        $service->shouldReceive('userDocumentTypesWithCampos')->once()->with($user)->andReturn(collect());

        $request = Mockery::mock(\App\Http\Requests\Document\IndexDocumentRequest::class);
        $request->shouldReceive('query')->once()->with('document_type_scope', '')->andReturn('');

        $controller = new DocumentController($service);
        $response = $controller->index($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_document_store_returns_json_on_success(): void
    {
        $service = Mockery::mock(DocumentService::class);
        $request = Mockery::mock(\App\Http\Requests\Document\CreateDocumentRequest::class);
        $document = new Document(['id' => 1]);
        $document->id = 1;

        $request->shouldReceive('validated')->once()->andReturn(['n_documento' => 'D-1']);
        $request->shouldReceive('file')->once()->with('root')->andReturn(null);
        $request->shouldReceive('input')->once()->with('campos', [])->andReturn([]);

        $service->shouldReceive('create')->once()->with(['n_documento' => 'D-1'], null, [])->andReturn($document);

        $controller = new DocumentController($service);
        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(201, $response->status());
    }

    public function test_document_update_returns_json_on_success(): void
    {
        $service = Mockery::mock(DocumentService::class);
        $request = Mockery::mock(\App\Http\Requests\Document\UpdateDocumentRequest::class);
        $document = new Document(['id' => 1]);
        $document->id = 1;
        $request->campos = [];

        $request->shouldReceive('validated')->once()->andReturn(['asunto' => 'Editado']);
        $request->shouldReceive('file')->once()->with('root')->andReturn(null);
        $request->shouldReceive('hasFile')->once()->with('root')->andReturn(false);

        $service->shouldReceive('update')->once()->andReturn($document);

        $controller = new DocumentController($service);
        $response = $controller->update($request, $document);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_document_upload_file_returns_json_on_success(): void
    {
        $service = Mockery::mock(DocumentService::class);
        $request = Mockery::mock(\App\Http\Requests\Document\UploadDocumentFileRequest::class);
        $document = new Document(['id' => 2]);
        $document->id = 2;

        $request->shouldReceive('file')->once()->with('root')->andReturn(null);
        $service->shouldReceive('uploadFile')->once()->with($document, null)->andReturn($document);

        $controller = new DocumentController($service);
        $response = $controller->uploadFile($request, $document);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_document_destroy_returns_json_on_success(): void
    {
        $service = Mockery::mock(DocumentService::class);
        $document = new Document(['id' => 3]);
        $document->id = 3;

        $service->shouldReceive('delete')->once()->with($document);

        $controller = new DocumentController($service);
        $response = $controller->destroy($document);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_document_generate_pdf_report_streams_pdf(): void
    {
        $service = Mockery::mock(DocumentService::class);
        $service->shouldReceive('report')->once()->andReturn(Document::query());

        Pdf::shouldReceive('loadView')->once()->with('documents.report', Mockery::type('array'))->andReturnSelf();
        Pdf::shouldReceive('setPaper')->once()->with('a4', 'landscape')->andReturnSelf();
        Pdf::shouldReceive('stream')->once()->with('reporte_documentos.pdf')->andReturn(response('pdf'));

        $controller = new DocumentController($service);
        $response = $controller->generatePDFReport(Request::create('/documents/pdf', 'GET'));

        $this->assertSame('pdf', $response->getContent());
    }

    public function test_block_index_returns_json(): void
    {
        $service = Mockery::mock(BlockService::class);
        $service->shouldReceive('getAll')->once()->andReturn([
            'blocks' => Block::query(),
            'areas' => collect(),
            'groups' => collect(),
            'subgroups' => collect(),
            'years' => [],
        ]);

        $controller = new BlockController($service);
        $response = $controller->index(Mockery::mock(\App\Http\Requests\Document\IndexBlockRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_block_store_returns_json_on_success(): void
    {
        $service = Mockery::mock(BlockService::class);
        $request = Mockery::mock(\App\Http\Requests\Document\CreateBlockRequest::class);
        $request->shouldReceive('validated')->once()->andReturn(['n_bloque' => 'B-1']);
        $request->shouldReceive('file')->once()->with('root')->andReturn(null);

        $service->shouldReceive('create')->once()->with(['n_bloque' => 'B-1'], null)->andReturn(new Block());

        $controller = new BlockController($service);
        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(201, $response->status());
    }

    public function test_block_update_returns_json_on_success(): void
    {
        $service = Mockery::mock(BlockService::class);
        $request = Mockery::mock(\App\Http\Requests\Document\UpdateBlockRequest::class);
        $block = new Block(['id' => 10]);
        $block->id = 10;

        $request->shouldReceive('validated')->once()->andReturn(['asunto' => 'ok']);
        $request->shouldReceive('file')->once()->with('root')->andReturn(null);
        $request->shouldReceive('hasFile')->once()->with('root')->andReturn(false);

        $service->shouldReceive('update')->once()->andReturn($block);

        $controller = new BlockController($service);
        $response = $controller->update($request, $block);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_block_upload_file_returns_json_on_success(): void
    {
        $service = Mockery::mock(BlockService::class);
        $request = Mockery::mock(\App\Http\Requests\Document\UploadBlockFileRequest::class);
        $block = new Block(['id' => 11]);
        $block->id = 11;

        $request->shouldReceive('file')->once()->with('root')->andReturn(null);
        $service->shouldReceive('uploadFile')->once()->with($block, null)->andReturn($block);

        $controller = new BlockController($service);
        $response = $controller->uploadFile($request, $block);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_block_destroy_returns_json_on_success(): void
    {
        $service = Mockery::mock(BlockService::class);
        $block = new Block(['id' => 12]);
        $block->id = 12;

        $service->shouldReceive('delete')->once()->with($block);

        $controller = new BlockController($service);
        $response = $controller->destroy($block);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_block_generate_pdf_report_streams_pdf(): void
    {
        $service = Mockery::mock(BlockService::class);
        $service->shouldReceive('report')->once()->andReturn(Block::query());

        Pdf::shouldReceive('loadView')->once()->with('blocks.report', Mockery::type('array'))->andReturnSelf();
        Pdf::shouldReceive('setPaper')->once()->with('a4', 'landscape')->andReturnSelf();
        Pdf::shouldReceive('stream')->once()->with('reporte_bloques.pdf')->andReturn(response('pdf'));

        $controller = new BlockController($service);
        $response = $controller->generatePDFReport(Request::create('/blocks/pdf', 'GET'));

        $this->assertSame('pdf', $response->getContent());
    }

    public function test_activity_log_index_returns_json(): void
    {
        $service = Mockery::mock(ActivityLogService::class);
        $service->shouldReceive('getIndexData')->once()->andReturn(['logs' => collect()]);

        $controller = new ActivityLogController($service);
        $response = $controller->index(Mockery::mock(\App\Http\Requests\ActivityLog\IndexActivityLogRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_activity_log_generate_pdf_streams_file(): void
    {
        $service = Mockery::mock(ActivityLogService::class);
        $service->shouldReceive('getReportLogs')->once()->andReturn(collect());

        Pdf::shouldReceive('loadView')->once()->with('activity_logs.report', Mockery::type('array'))->andReturnSelf();
        Pdf::shouldReceive('setPaper')->once()->with('a4', 'landscape')->andReturnSelf();
        Pdf::shouldReceive('stream')->once()->with('Reporte_Actividades.pdf')->andReturn(response('pdf'));

        $controller = new ActivityLogController($service);
        $response = $controller->generatePDF(Request::create('/activity-logs/pdf', 'GET'));

        $this->assertSame('pdf', $response->getContent());
    }

    public function test_inbox_index_returns_json(): void
    {
        $service = Mockery::mock(InboxService::class);
        $service->shouldReceive('getIndexData')->once()->andReturn(['blocks' => collect()]);

        $controller = new InboxController($service);
        $response = $controller->index(Mockery::mock(\App\Http\Requests\Inbox\IndexInboxRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_inbox_update_block_storage_validates_and_returns_json(): void
    {
        $service = Mockery::mock(InboxService::class);
        $service->shouldReceive('updateBlockStorage')->once();

        $controller = new InboxController($service);
        $request = Request::create('/inbox/update-storage/1', 'PUT', [
            'n_box' => 1,
            'n_andamio' => '2',
            'n_section' => '3',
        ]);

        $response = $controller->updateBlockStorage($request, 1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_notification_index_returns_json(): void
    {
        $service = Mockery::mock(NotificationService::class);
        $service->shouldReceive('getNotifications')->once()->andReturn(collect());

        $controller = new NotificationController($service);
        $response = $controller->index(Mockery::mock(\App\Http\Requests\Notification\IndexNotificationRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_notification_redirect_rejects_when_not_owner(): void
    {
        $service = Mockery::mock(NotificationService::class);
        $notification = new class {
            public $read_at = null;
            public function markAsRead(): void
            {
            }
        };

        $service->shouldReceive('findNotificationOrFail')->once()->with('abc')->andReturn($notification);
        $service->shouldReceive('isNotificationOwner')->once()->with($notification)->andReturn(false);

        $controller = new NotificationController($service);
        $response = $controller->redirectAndMarkAsRead('abc');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(403, $response->status());
    }

    public function test_notification_redirect_marks_as_read_for_owner(): void
    {
        $service = Mockery::mock(NotificationService::class);
        $notification = new class {
            public $read_at = null;
            public bool $marked = false;
            public function markAsRead(): void
            {
                $this->marked = true;
                $this->read_at = now();
            }
        };

        $service->shouldReceive('findNotificationOrFail')->once()->with(55)->andReturn($notification);
        $service->shouldReceive('isNotificationOwner')->once()->with($notification)->andReturn(true);

        $controller = new NotificationController($service);
        $response = $controller->redirectAndMarkAsRead(55);

        $this->assertTrue($notification->marked);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Tester',
            'last_name' => 'Controllers',
            'user_name' => 'tester_' . Str::lower(Str::random(8)),
            'dni' => (string) random_int(10000000, 99999999),
            'email' => Str::lower(Str::random(8)) . '@example.com',
            'password' => 'password',
        ], $attributes));
    }
}
