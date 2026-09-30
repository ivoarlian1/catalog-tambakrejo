<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_creates_catalog_with_stable_slug_and_controls_publication(): void
    {
        $admin = User::factory()->superadmin()->create();

        $response = $this->actingAs($admin)->post(route('superadmin.catalogs.store'), [
            'name' => 'Warung Bu Siti',
            'type' => 'umkm',
            'sub_type' => 'Kuliner',
            'owner_name' => 'Siti Aminah',
            'address' => 'Jl. Tambakrejo No. 1',
            'status' => 'draft',
        ]);

        $catalog = Catalog::query()->where('name', 'Warung Bu Siti')->firstOrFail();
        $response->assertRedirect(route('superadmin.catalogs.show', $catalog));
        $this->assertSame('warung-bu-siti', $catalog->slug);

        $this->put(route('superadmin.catalogs.update', $catalog), [
            'name' => $catalog->name,
            'slug' => 'changed-by-client',
            'type' => $catalog->type,
            'sub_type' => $catalog->sub_type,
            'owner_name' => $catalog->owner_name,
            'address' => $catalog->address,
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('catalogs', [
            'id' => $catalog->id,
            'slug' => 'warung-bu-siti',
        ]);

        $this->post(route('superadmin.catalogs.publish', $catalog));
        $this->assertDatabaseHas('catalogs', ['id' => $catalog->id, 'status' => 'published']);

        $this->post(route('superadmin.catalogs.unpublish', $catalog));
        $this->assertDatabaseHas('catalogs', ['id' => $catalog->id, 'status' => 'draft']);
    }
}
