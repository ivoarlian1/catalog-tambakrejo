<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_creates_category_and_uses_it_with_a_reusable_custom_subcategory(): void
    {
        $admin = User::factory()->superadmin()->create();
        $this->actingAs($admin);

        $this->post(route('superadmin.categories.store'), [
            'name' => 'Pariwisata',
            'is_active' => '1',
        ])->assertRedirect(route('superadmin.categories.index'));

        $category = Category::query()->where('slug', 'pariwisata')->firstOrFail();
        $this->get(route('superadmin.catalogs.create'))->assertSee('Pariwisata');
        $this->get(route('home'))->assertSee('Pariwisata');

        foreach ([0, 1] as $index) {
            $this->post(route('superadmin.catalogs.store'), [
                'name' => 'Destinasi '.$index,
                'type' => $category->slug,
            'sub_type' => 'Pantai dan Wisata Bahari',
                'responsible_person' => 'Pengelola Wisata',
                'address' => 'Kelurahan Tambakrejo',
                'status' => 'published',
            ])->assertRedirect();
        }

        $this->assertDatabaseHas('catalogs', [
            'name' => 'Destinasi 0',
            'type' => 'pariwisata',
            'sub_type' => 'Pantai dan Wisata Bahari',
        ]);
        $this->assertDatabaseHas('category_subcategories', [
            'category_id' => $category->id,
            'name' => 'Pantai dan Wisata Bahari',
        ]);
        $this->assertSame(1, $category->subcategories()->where('name', 'Pantai dan Wisata Bahari')->count());

        $this->get(route('katalog.bytype', 'pariwisata').'?sub_type='.urlencode('Pantai dan Wisata Bahari'))
            ->assertSee('Destinasi 0')
            ->assertSee('Destinasi 1');
        $this->get(route('katalog.index').'?search=Pariwisata')->assertSee('Destinasi 0');
    }

    public function test_category_name_can_change_without_changing_its_catalog_urls(): void
    {
        $admin = User::factory()->superadmin()->create();
        $category = Category::query()->where('slug', 'health')->firstOrFail();
        $catalog = Catalog::factory()->create(['type' => $category->slug, 'slug' => 'puskesmas-lama']);

        $this->actingAs($admin)->put(route('superadmin.categories.update', $category), [
            'name' => 'Layanan Kesehatan',
            'sort_order' => 30,
            'is_active' => '1',
        ])->assertRedirect(route('superadmin.categories.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'slug' => 'health', 'name' => 'Layanan Kesehatan']);
        $this->get(route('katalog.show', ['type' => 'health', 'slug' => $catalog->slug]))->assertOk();
    }

    public function test_category_in_use_cannot_be_deleted_but_can_be_deactivated(): void
    {
        $admin = User::factory()->superadmin()->create();
        $category = Category::query()->where('slug', 'education')->firstOrFail();
        Catalog::factory()->create(['type' => $category->slug]);

        $this->actingAs($admin)->delete(route('superadmin.categories.destroy', $category))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->put(route('superadmin.categories.update', $category), [
            'name' => $category->name,
            'sort_order' => $category->sort_order,
        ])->assertRedirect(route('superadmin.categories.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'is_active' => false]);

        $this->post(route('superadmin.catalogs.store'), [
            'name' => 'New Education Catalog',
            'type' => $category->slug,
            'sub_type' => 'Sekolah Alternatif',
            'responsible_person' => 'Pengelola',
            'address' => 'Kelurahan Tambakrejo',
            'status' => 'draft',
        ])->assertInvalid(['type']);
    }

    public function test_unused_category_can_be_deleted(): void
    {
        $admin = User::factory()->superadmin()->create();
        $category = Category::query()->create([
            'name' => 'Olahraga',
            'slug' => 'olahraga',
            'is_active' => true,
            'sort_order' => 50,
        ]);

        $this->actingAs($admin)->delete(route('superadmin.categories.destroy', $category))
            ->assertRedirect(route('superadmin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_catalog_admin_can_update_own_profile_when_its_category_is_inactive(): void
    {
        $category = Category::query()->where('slug', 'education')->firstOrFail();
        $category->update(['is_active' => false]);
        $catalog = Catalog::factory()->create([
            'type' => $category->slug,
            'responsible_person' => 'Kepala Sekolah',
        ]);
        $admin = User::factory()->catalogAdmin($catalog)->create();

        $this->actingAs($admin)->put(route('catalog-admin.catalog.update'), [
            'name' => 'Nama Pendidikan Diperbarui',
            'sub_type' => 'Sekolah Alternatif',
            'responsible_person' => 'Kepala Sekolah Baru',
            'address' => $catalog->address,
        ])->assertRedirect(route('catalog-admin.catalog.edit'));

        $this->assertDatabaseHas('catalogs', [
            'id' => $catalog->id,
            'type' => 'education',
            'name' => 'Nama Pendidikan Diperbarui',
            'responsible_person' => 'Kepala Sekolah Baru',
        ]);
    }
}
