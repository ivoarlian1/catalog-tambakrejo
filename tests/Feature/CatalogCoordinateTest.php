<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogCoordinateTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_update_rejects_coordinates_outside_geographic_ranges(): void
    {
        $admin = User::factory()->superadmin()->create();
        $catalog = Catalog::factory()->umkm()->create();

        $response = $this->actingAs($admin)->put(route('superadmin.catalogs.update', $catalog), [
            'name' => $catalog->name,
            'slug' => $catalog->slug,
            'type' => 'umkm',
            'sub_type' => $catalog->sub_type,
            'status' => $catalog->status,
            'owner_name' => $catalog->owner_name,
            'address' => $catalog->address,
            'latitude' => '90.0001',
            'longitude' => '181',
        ]);

        $response->assertInvalid(['latitude', 'longitude']);
        $this->assertDatabaseHas('catalogs', [
            'id' => $catalog->id,
            'latitude' => null,
            'longitude' => null,
        ]);
    }

    public function test_catalog_update_persists_valid_coordinates(): void
    {
        $admin = User::factory()->superadmin()->create();
        $catalog = Catalog::factory()->umkm()->create();

        $response = $this->actingAs($admin)->put(route('superadmin.catalogs.update', $catalog), [
            'name' => $catalog->name,
            'slug' => $catalog->slug,
            'type' => 'umkm',
            'sub_type' => $catalog->sub_type,
            'status' => $catalog->status,
            'owner_name' => $catalog->owner_name,
            'address' => $catalog->address,
            'latitude' => '-6.1234567',
            'longitude' => '110.1234567',
        ]);

        $response->assertRedirect(route('superadmin.catalogs.show', $catalog));
        $this->assertDatabaseHas('catalogs', [
            'id' => $catalog->id,
            'latitude' => '-6.1234567',
            'longitude' => '110.1234567',
        ]);
    }
}
