<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Andamio;
use App\Models\Box;
use App\Models\Block;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StorageGlobalFinderTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        
        Permission::firstOrCreate(['name' => 'sections.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'andamios.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'boxes.view', 'guard_name' => 'web']);

        $adminRole->givePermissionTo(Permission::all());

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    public function test_searching_block_code_in_sections_finds_section_and_block_path()
    {
        $section = Section::factory()->create(['n_section' => 'SEC-FIND']);
        $andamio = Andamio::factory()->create(['section_id' => $section->id, 'n_andamio' => 'AND-FIND']);
        $box = Box::factory()->create(['andamio_id' => $andamio->id, 'n_box' => 'BOX-FIND']);
        
        Block::factory()->create([
            'box_id' => $box->id,
            'n_bloque' => 'B-FIND-123',
            'asunto' => 'Expediente Confidencial'
        ]);

        // Buscar en /sections
        $response = $this->actingAs($this->adminUser)->get('/sections?search=FIND-123');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('storage/index')
            ->has('sections', 1)
            ->where('sections.0.n_section', 'SEC-FIND')
            ->has('searchedBlocks', 1)
            ->where('searchedBlocks.0.n_bloque', 'B-FIND-123')
            ->where('searchedBlocks.0.path.box.n_box', 'BOX-FIND')
        );
    }
}
