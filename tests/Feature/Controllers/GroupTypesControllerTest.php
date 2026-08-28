<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\GroupType;
use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GroupTypesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('ADMINISTRADOR');
    }

    /** @test */
    public function admin_can_access_group_types_index()
    {
        GroupType::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->get('/tipos-grupos');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('group_types/index')
                ->has('groupTypes.data', 2)
            );
    }

    /** @test */
    public function admin_can_create_group_type()
    {
        $data = ['descripcion' => 'Tipo Test', 'abreviacion' => 'TT'];

        // La ruta segun routes/areas.php es /tipos-grupos
        $response = $this->actingAs($this->admin)->post('/tipos-grupos', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('group_types', $data);
    }

    /** @test */
    public function cannot_delete_group_type_in_use()
    {
        $gt = GroupType::factory()->create();
        $area = Area::factory()->create();
        
        $agt = AreaGroupType::create([
            'area_id' => $area->id,
            'group_type_id' => $gt->id
        ]);

        // Crear un grupo asociado real
        Group::factory()->create(['area_group_type_id' => $agt->id]);

        // La ruta de eliminacion usa {id}
        $response = $this->actingAs($this->admin)->delete("/tipos-grupos/{$gt->id}");

        $response->assertSessionHas('error', 'No se puede eliminar este tipo de grupo porque tiene grupos asociados.');
        $this->assertDatabaseHas('group_types', ['id' => $gt->id]);
    }
}
