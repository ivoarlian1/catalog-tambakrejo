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
            'sort_order' => $catalog->sort_order,
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
            'sort_order' => $catalog->sort_order,
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
            'coordinate_format' => 'latitude_longitude',
            'coordinate_x' => '110.1234567',
            'coordinate_y' => '-6.1234567',
            'latitude' => '-6.1234567',
            'longitude' => '110.1234567',
        ]);
    }

    public function test_superadmin_can_create_catalog_with_utm_xy_coordinates(): void
    {
        $admin = User::factory()->superadmin()->create();

        $this->actingAs($admin)
            ->get(route('superadmin.catalogs.create'))
            ->assertSee('XY — UTM zona 49S / WGS84 (meter)')
            ->assertSee('Latitude / Longitude (derajat)');

        $response = $this->post(route('superadmin.catalogs.store'), [
            'name' => 'Katalog dengan koordinat UTM',
            'sort_order' => 80,
            'type' => 'umkm',
            'sub_type' => 'Kuliner',
            'owner_name' => 'Pemilik Usaha',
            'address' => 'Jl. Tambakrejo No. 8',
            'status' => 'published',
            'coordinate_format' => 'utm_49s',
            'coordinate_x' => '438338.24733543483',
            'coordinate_y' => '9231029.95716361',
        ]);

        $catalog = Catalog::query()->where('name', 'Katalog dengan koordinat UTM')->firstOrFail();
        $response->assertRedirect(route('superadmin.catalogs.show', $catalog));
        $this->assertDatabaseHas('catalogs', [
            'id' => $catalog->id,
            'coordinate_format' => 'utm_49s',
            'longitude' => '110.4417879',
        ]);
        $catalog->refresh();
        $this->assertSame(-6.9564418, round((float) $catalog->latitude, 7));
        $this->assertSame(438338.2473354, round((float) $catalog->coordinate_x, 7));
        $this->assertSame(9231029.9571636, round((float) $catalog->coordinate_y, 7));

        $this->get(route('katalog.show', ['type' => 'umkm', 'slug' => $catalog->slug]))
            ->assertSee('Koordinat Lokasi — X, Y (UTM zona 49S / WGS84)')
            ->assertSee('438338.2473354, 9231029.9571636');
    }

    public function test_catalog_update_rejects_xy_outside_utm_zone_ranges(): void
    {
        $admin = User::factory()->superadmin()->create();
        $catalog = Catalog::factory()->umkm()->create();

        $this->actingAs($admin)->put(route('superadmin.catalogs.update', $catalog), [
            'name' => $catalog->name,
            'sort_order' => $catalog->sort_order,
            'type' => $catalog->type,
            'sub_type' => $catalog->sub_type,
            'status' => $catalog->status,
            'owner_name' => $catalog->owner_name,
            'address' => $catalog->address,
            'coordinate_format' => 'utm_49s',
            'coordinate_x' => '99999',
            'coordinate_y' => '9231029',
        ])->assertInvalid(['coordinate_x']);

        $this->assertDatabaseHas('catalogs', [
            'id' => $catalog->id,
            'coordinate_format' => 'latitude_longitude',
            'coordinate_x' => null,
            'coordinate_y' => null,
            'latitude' => null,
            'longitude' => null,
        ]);
    }

    public function test_catalog_admin_can_change_their_catalog_coordinate_format(): void
    {
        $catalog = Catalog::factory()->umkm()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create();

        $this->actingAs($admin)->put(route('catalog-admin.catalog.update'), [
            'name' => $catalog->name,
            'sort_order' => $catalog->sort_order,
            'sub_type' => $catalog->sub_type,
            'owner_name' => $catalog->owner_name,
            'address' => $catalog->address,
            'coordinate_format' => 'utm_49s',
            'coordinate_x' => '438338.24733543483',
            'coordinate_y' => '9231029.95716361',
        ])->assertRedirect(route('catalog-admin.catalog.edit'));

        $catalog->refresh();
        $this->assertSame('utm_49s', $catalog->coordinate_format);
        $this->assertSame(-6.9564418, round((float) $catalog->latitude, 7));
        $this->assertSame(110.4417879, round((float) $catalog->longitude, 7));
    }
}
