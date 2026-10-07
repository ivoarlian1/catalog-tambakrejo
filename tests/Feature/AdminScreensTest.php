<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScreensTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_management_screens_render(): void
    {
        $catalog = Catalog::factory()->umkm()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create();
        $product = Product::factory()->for($catalog)->create();
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin);

        $this->get(route('superadmin.dashboard'))->assertOk();
        $this->get(route('superadmin.categories.index'))->assertOk();
        $this->get(route('superadmin.categories.create'))->assertOk();
        $this->get(route('superadmin.categories.edit', \App\Models\Category::query()->firstOrFail()))->assertOk();
        $this->get(route('superadmin.catalogs.index'))->assertOk();
        $this->get(route('superadmin.catalogs.create'))->assertOk();
        $this->get(route('superadmin.catalogs.show', $catalog))->assertOk();
        $this->get(route('superadmin.catalogs.edit', $catalog))->assertOk();
        $this->get(route('superadmin.catalogs.qr', $catalog))->assertOk();
        $this->get(route('superadmin.admins.index'))->assertOk();
        $this->get(route('superadmin.admins.create'))->assertOk();
        $this->get(route('superadmin.admins.edit', $admin))->assertOk();
        $this->get(route('superadmin.superadmins.index'))->assertOk();
        $this->get(route('superadmin.superadmins.create'))->assertOk();
        $this->get(route('superadmin.products.index'))->assertOk();
        $this->get(route('superadmin.catalogs.products.index', $catalog))->assertOk();
        $this->get(route('superadmin.catalogs.products.create', $catalog))->assertOk();
        $this->get(route('superadmin.catalogs.products.edit', [$catalog, $product]))->assertOk();
    }

    public function test_catalog_admin_screens_render_for_their_assigned_catalog(): void
    {
        $catalog = Catalog::factory()->umkm()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create();
        Product::factory()->for($catalog)->create();

        $this->actingAs($admin);

        $this->get(route('catalog-admin.dashboard'))->assertOk();
        $this->get(route('catalog-admin.catalog.edit'))->assertOk();
        $this->get(route('catalog-admin.products.index'))->assertOk();
        $this->get(route('catalog-admin.products.create'))->assertOk();
        $this->get(route('catalog-admin.products.edit', $catalog->products()->firstOrFail()))->assertOk();
    }
}
