<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\Areas\AreaController;
use App\Http\Controllers\Areas\GroupController;
use App\Http\Controllers\Areas\GroupTypeController;
use App\Http\Controllers\Areas\SubgroupController;
use App\Http\Controllers\Storage\AndamioController;
use App\Http\Controllers\Storage\ArchivoController;
use App\Http\Controllers\Storage\BoxController;
use App\Http\Controllers\Storage\SectionController;
use App\Models\Andamio;
use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\Box;
use App\Models\Group;
use App\Models\GroupType;
use App\Models\Section;
use App\Models\Subgroup;
use App\Services\Areas\AreaService;
use App\Services\Areas\GroupService;
use App\Services\Areas\GroupTypeService;
use App\Services\Areas\SubgroupService;
use App\Services\Storage\AndamioService;
use App\Services\Storage\ArchivoService;
use App\Services\Storage\BoxService;
use App\Services\Storage\SectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class AreasAndStorageControllersTest extends TestCase
{
    use RefreshDatabase;

    public function test_area_index_returns_json(): void
    {
        $service = Mockery::mock(AreaService::class);
        $service->shouldReceive('getIndexData')->once()->andReturn([
            'areas' => Area::query()->paginate(10),
        ]);

        $controller = new AreaController($service);
        $response = $controller->index(Mockery::mock(\App\Http\Requests\Area\IndexAreaRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_area_create_returns_method_not_allowed_json(): void
    {
        $controller = new AreaController(Mockery::mock(AreaService::class));
        $response = $controller->create();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(405, $response->status());
    }

    public function test_area_store_returns_json(): void
    {
        $service = Mockery::mock(AreaService::class);
        $request = Mockery::mock(\App\Http\Requests\Area\CreateAreaRequest::class);
        $service->shouldReceive('create')->once()->with($request);

        $controller = new AreaController($service);
        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(201, $response->status());
    }

    public function test_area_show_returns_json(): void
    {
        $service = Mockery::mock(AreaService::class);
        $area = Area::create(['descripcion' => 'Area 1', 'abreviacion' => 'A1']);
        $service->shouldReceive('getShowData')->once()->with($area)->andReturn(['area' => $area]);

        $controller = new AreaController($service);
        $response = $controller->show($area);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_area_edit_returns_json(): void
    {
        $service = Mockery::mock(AreaService::class);
        $area = Area::create(['descripcion' => 'Area 2', 'abreviacion' => 'A2']);
        $service->shouldReceive('getEditData')->once()->with($area)->andReturn(['area' => $area]);

        $controller = new AreaController($service);
        $response = $controller->edit($area);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_area_update_validates_and_returns_json(): void
    {
        $service = Mockery::mock(AreaService::class);
        $area = Area::create(['descripcion' => 'Area 3', 'abreviacion' => 'A3']);
        $service->shouldReceive('update')->once()->with($area, Mockery::type('array'));

        $controller = new AreaController($service);
        $request = Request::create("/areas/{$area->id}", 'PUT', [
            'descripcion' => 'Area 3 editada',
            'abreviacion' => 'A3E',
        ]);

        $response = $controller->update($request, $area);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_area_destroy_returns_json(): void
    {
        $service = Mockery::mock(AreaService::class);
        $area = Area::create(['descripcion' => 'Area 4', 'abreviacion' => 'A4']);
        $service->shouldReceive('delete')->once()->with($area);

        $controller = new AreaController($service);
        $response = $controller->destroy($area);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_group_store_returns_json(): void
    {
        $service = Mockery::mock(GroupService::class);
        $request = Mockery::mock(\App\Http\Requests\Group\CreateGroupRequest::class);
        $service->shouldReceive('create')->once()->with($request);

        $controller = new GroupController($service);
        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(201, $response->status());
    }

    public function test_group_edit_returns_json(): void
    {
        $service = Mockery::mock(GroupService::class);
        $group = Group::create([
            'area_group_type_id' => $this->createAreaGraph()['areaGroupType']->id,
            'descripcion' => 'Grupo 1',
            'abreviacion' => 'G1',
        ]);
        $service->shouldReceive('find')->once()->with($group->id)->andReturn($group);

        $controller = new GroupController($service);
        $response = $controller->edit((string) $group->id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_group_update_returns_json(): void
    {
        $service = Mockery::mock(GroupService::class);
        $graph = $this->createAreaGraph();
        $group = $graph['group'];

        $request = Mockery::mock(\App\Http\Requests\Group\UpdateGroupRequest::class);
        $request->shouldReceive('all')->once()->andReturn(['descripcion' => 'Nuevo']);

        $service->shouldReceive('find')->once()->with($group->id)->andReturn($group);
        $service->shouldReceive('update')->once()->with($group, ['descripcion' => 'Nuevo']);

        $controller = new GroupController($service);
        $response = $controller->update($request, (string) $group->id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_group_destroy_returns_json(): void
    {
        $service = Mockery::mock(GroupService::class);
        $group = $this->createAreaGraph()['group'];

        $service->shouldReceive('find')->once()->with($group->id)->andReturn($group);
        $service->shouldReceive('delete')->once()->with($group);

        $controller = new GroupController($service);
        $response = $controller->destroy((string) $group->id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_group_type_index_create_store_edit_update_destroy(): void
    {
        $service = Mockery::mock(GroupTypeService::class);
        $service->shouldReceive('getAll')->once()->with(null)->andReturn(collect());
        $service->shouldReceive('create')->once()->with(['descripcion' => 'Tipo A', 'abreviacion' => 'TA']);

        $groupType = GroupType::create(['descripcion' => 'Tipo B', 'abreviacion' => 'TB']);
        $service->shouldReceive('find')->times(3)->andReturn($groupType);
        $service->shouldReceive('update')->once()->with($groupType, ['descripcion' => 'Tipo C', 'abreviacion' => 'TC']);
        $service->shouldReceive('delete')->once()->with($groupType);

        $controller = new GroupTypeController($service);

        $indexRequest = Mockery::mock(\App\Http\Requests\GroupType\IndexGroupTypeRequest::class);
        $indexRequest->shouldReceive('input')->once()->with('search')->andReturn(null);

        $index = $controller->index($indexRequest);
        $create = $controller->create();
        $store = $controller->store(Mockery::mock(\App\Http\Requests\GroupType\CreateGroupTypeRequest::class, function ($mock) {
            $mock->shouldReceive('all')->andReturn(['descripcion' => 'Tipo A', 'abreviacion' => 'TA']);
        }));
        $edit = $controller->edit((string) $groupType->id);
        $update = $controller->update(Mockery::mock(\App\Http\Requests\GroupType\UpdateGroupTypeRequest::class, function ($mock) {
            $mock->shouldReceive('all')->andReturn(['descripcion' => 'Tipo C', 'abreviacion' => 'TC']);
        }), (string) $groupType->id);
        $destroy = $controller->destroy((string) $groupType->id);

        $this->assertInstanceOf(JsonResponse::class, $index);
        $this->assertInstanceOf(JsonResponse::class, $create);
        $this->assertSame(405, $create->status());
        $this->assertInstanceOf(JsonResponse::class, $store);
        $this->assertSame(201, $store->status());
        $this->assertInstanceOf(JsonResponse::class, $edit);
        $this->assertInstanceOf(JsonResponse::class, $update);
        $this->assertInstanceOf(JsonResponse::class, $destroy);
    }

    public function test_subgroup_store_edit_update_destroy_are_executed(): void
    {
        $service = Mockery::mock(SubgroupService::class);
        $graph = $this->createAreaGraph();
        $subgroup = $graph['subgroup'];

        $service->shouldReceive('create')->once();
        $service->shouldReceive('find')->times(3)->with($subgroup->id)->andReturn($subgroup);
        $service->shouldReceive('update')->once()->with($subgroup, ['descripcion' => 'Sub editado']);
        $service->shouldReceive('delete')->once()->with($subgroup);

        $controller = new SubgroupController($service);

        $store = $controller->store(Mockery::mock(\App\Http\Requests\Subgroup\CreateSubgroupRequest::class));
        $edit = $controller->edit((string) $subgroup->id);
        $update = $controller->update(Mockery::mock(\App\Http\Requests\Subgroup\UpdateSubgroupRequest::class, function ($mock) {
            $mock->shouldReceive('all')->andReturn(['descripcion' => 'Sub editado']);
        }), (string) $subgroup->id);
        $destroy = $controller->destroy((string) $subgroup->id);

        $this->assertInstanceOf(JsonResponse::class, $store);
        $this->assertSame(201, $store->status());
        $this->assertInstanceOf(JsonResponse::class, $edit);
        $this->assertInstanceOf(JsonResponse::class, $update);
        $this->assertInstanceOf(JsonResponse::class, $destroy);
    }

    public function test_section_index_store_update_destroy_are_executed(): void
    {
        $service = Mockery::mock(SectionService::class);
        $section = Section::create(['n_section' => '1', 'descripcion' => 'S1']);
        $service->shouldReceive('getAll')->once()->with(null)->andReturn(collect([$section]));
        $service->shouldReceive('create')->once()->with(['n_section' => '2', 'descripcion' => 'S2']);
        $service->shouldReceive('update')->once()->with($section, ['n_section' => '1', 'descripcion' => 'S1 edit']);
        $service->shouldReceive('delete')->once()->with($section);

        $controller = new SectionController($service);

        $indexRequest = Mockery::mock(\App\Http\Requests\Storage\IndexSectionRequest::class);
        $indexRequest->shouldReceive('input')->once()->with('search')->andReturn(null);

        $index = $controller->index($indexRequest);
        $store = $controller->store(Request::create('/sections', 'POST', ['n_section' => '2', 'descripcion' => 'S2']));
        $update = $controller->update(Request::create("/sections/{$section->id}", 'PUT', [
            'n_section' => '1',
            'descripcion' => 'S1 edit',
        ]), $section);
        $destroy = $controller->destroy($section);

        $this->assertInstanceOf(JsonResponse::class, $index);
        $this->assertInstanceOf(JsonResponse::class, $store);
        $this->assertSame(201, $store->status());
        $this->assertInstanceOf(JsonResponse::class, $update);
        $this->assertInstanceOf(JsonResponse::class, $destroy);
    }

    public function test_andamio_index_store_update_destroy_are_executed(): void
    {
        $service = Mockery::mock(AndamioService::class);
        $section = Section::create(['n_section' => '10', 'descripcion' => 'Main']);
        $andamio = Andamio::create(['n_andamio' => 1, 'descripcion' => 'A1', 'section_id' => $section->id]);

        $service->shouldReceive('getBySection')->once()->with($section, null)->andReturn(collect([$andamio]));
        $service->shouldReceive('create')->once()->with($section, ['n_andamio' => 2, 'descripcion' => 'A2']);
        $service->shouldReceive('update')->once()->with($andamio, ['n_andamio' => 1, 'descripcion' => 'A1 edit']);
        $service->shouldReceive('delete')->once()->with($andamio);

        $controller = new AndamioController($service);

        $indexRequest = Mockery::mock(\App\Http\Requests\Storage\IndexAndamioRequest::class);
        $indexRequest->shouldReceive('input')->once()->with('search')->andReturn(null);

        $index = $controller->index($indexRequest, $section);
        $store = $controller->store(Request::create('/x', 'POST', ['n_andamio' => 2, 'descripcion' => 'A2']), $section);
        $update = $controller->update(Request::create('/x', 'PUT', [
            'n_andamio' => 1,
            'descripcion' => 'A1 edit',
        ]), $section, $andamio);
        $destroy = $controller->destroy($section, $andamio);

        $this->assertInstanceOf(JsonResponse::class, $index);
        $this->assertInstanceOf(JsonResponse::class, $store);
        $this->assertSame(201, $store->status());
        $this->assertInstanceOf(JsonResponse::class, $update);
        $this->assertInstanceOf(JsonResponse::class, $destroy);
    }

    public function test_box_index_store_update_destroy_are_executed(): void
    {
        $service = Mockery::mock(BoxService::class);
        $section = Section::create(['n_section' => '20', 'descripcion' => 'Main']);
        $andamio = Andamio::create(['n_andamio' => 5, 'descripcion' => 'A5', 'section_id' => $section->id]);
        $box = Box::create(['n_box' => '1', 'andamio_id' => $andamio->id]);

        $service->shouldReceive('getByAndamio')->once()->with($andamio, null)->andReturn(collect([$box]));
        $service->shouldReceive('create')->once()->with($andamio, ['n_box' => '2']);
        $service->shouldReceive('update')->once()->with($box, ['n_box' => '1A']);
        $service->shouldReceive('delete')->once()->with($box);

        $controller = new BoxController($service);

        $indexRequest = Mockery::mock(\App\Http\Requests\Storage\IndexBoxRequest::class);
        $indexRequest->shouldReceive('input')->once()->with('search')->andReturn(null);

        $index = $controller->index($indexRequest, $section, $andamio);
        $store = $controller->store(Request::create('/x', 'POST', ['n_box' => '2']), $section, $andamio);
        $update = $controller->update(Request::create('/x', 'PUT', ['n_box' => '1A']), $section, $andamio, $box);
        $destroy = $controller->destroy($section, $andamio, $box);

        $this->assertInstanceOf(JsonResponse::class, $index);
        $this->assertInstanceOf(JsonResponse::class, $store);
        $this->assertSame(201, $store->status());
        $this->assertInstanceOf(JsonResponse::class, $update);
        $this->assertInstanceOf(JsonResponse::class, $destroy);
    }

    public function test_archivo_index_and_move_to_default_are_executed(): void
    {
        $service = Mockery::mock(ArchivoService::class);
        $box = new Box(['id' => 1, 'n_box' => 'X']);
        $box->id = 1;
        $blocks = collect();

        $service->shouldReceive('getBoxWithBlocks')->once()->with(1, null)->andReturn([
            'box' => $box,
            'blocks' => $blocks,
        ]);
        $service->shouldReceive('moveToDefault')->once()->with(1, 99);

        $controller = new ArchivoController($service);

        $indexRequest = Mockery::mock(\App\Http\Requests\Storage\IndexArchivoRequest::class);
        $indexRequest->shouldReceive('input')->once()->with('search')->andReturn(null);

        $index = $controller->index($indexRequest, 1, 1, 1);
        $move = $controller->moveToDefault(1, 1, 1, 99);

        $this->assertInstanceOf(JsonResponse::class, $index);
        $this->assertInstanceOf(JsonResponse::class, $move);
        $this->assertSame(200, $move->status());
    }

    private function createAreaGraph(): array
    {
        $area = Area::create(['descripcion' => 'Area test', 'abreviacion' => 'AT']);
        $groupType = GroupType::create(['descripcion' => 'Tipo test', 'abreviacion' => 'TT']);
        $areaGroupType = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $groupType->id]);
        $group = Group::create([
            'area_group_type_id' => $areaGroupType->id,
            'descripcion' => 'Grupo test',
            'abreviacion' => 'GT',
        ]);
        $subgroup = Subgroup::create([
            'group_id' => $group->id,
            'descripcion' => 'Subgrupo test',
            'abreviacion' => 'ST',
        ]);

        return compact('area', 'groupType', 'areaGroupType', 'group', 'subgroup');
    }
}
