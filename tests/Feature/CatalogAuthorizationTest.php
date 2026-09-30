<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_admin_cannot_open_another_catalogs_product(): void
    {
        $ownCatalog = Catalog::factory()->umkm()->create();
        $otherCatalog = Catalog::factory()->umkm()->create();
        $admin = User::factory()->catalogAdmin($ownCatalog)->create();
        $otherProduct = Product::factory()->for($otherCatalog)->create();

        $this->actingAs($admin)
            ->get(route('catalog-admin.products.edit', $otherProduct))
            ->assertForbidden();
    }

    public function test_catalog_admin_product_creation_uses_their_catalog_id(): void
    {
        $ownCatalog = Catalog::factory()->umkm()->create();
        $otherCatalog = Catalog::factory()->umkm()->create();
        $admin = User::factory()->catalogAdmin($ownCatalog)->create();

        $response = $this->actingAs($admin)->post(route('catalog-admin.products.store'), [
            'name' => 'Produk milik katalog admin',
            'description' => 'Deskripsi produk.',
            'price' => 12500,
            'catalog_id' => $otherCatalog->id,
            'is_available' => '1',
        ]);

        $response->assertRedirect(route('catalog-admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Produk milik katalog admin',
            'catalog_id' => $ownCatalog->id,
        ]);
        $this->assertDatabaseMissing('products', [
            'name' => 'Produk milik katalog admin',
            'catalog_id' => $otherCatalog->id,
        ]);
    }

    public function test_catalog_admin_cannot_access_superadmin_dashboard(): void
    {
        $catalog = Catalog::factory()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create();

        $this->actingAs($admin)
            ->get(route('superadmin.dashboard'))
            ->assertForbidden();
    }

    public function test_catalog_admin_profile_update_ignores_catalog_and_slug_input(): void
    {
        $ownCatalog = Catalog::factory()->umkm()->create();
        $otherCatalog = Catalog::factory()->umkm()->create();
        $admin = User::factory()->catalogAdmin($ownCatalog)->create();

        $response = $this->actingAs($admin)->put(route('catalog-admin.catalog.update'), [
            'name' => 'Nama usaha baru',
            'slug' => 'stolen-slug',
            'type' => 'public_facility',
            'sub_type' => $ownCatalog->sub_type,
            'owner_name' => $ownCatalog->owner_name,
            'address' => $ownCatalog->address,
            'catalog_id' => $otherCatalog->id,
            'status' => 'published',
        ]);

        $response->assertRedirect(route('catalog-admin.catalog.edit'));
        $this->assertDatabaseHas('catalogs', [
            'id' => $ownCatalog->id,
            'name' => 'Nama usaha baru',
            'slug' => $ownCatalog->slug,
            'type' => 'umkm',
            'status' => $ownCatalog->status,
        ]);
        $this->assertDatabaseMissing('catalogs', [
            'id' => $ownCatalog->id,
            'catalog_id' => $otherCatalog->id,
        ]);
    }
}
