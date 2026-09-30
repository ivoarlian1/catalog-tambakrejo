<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\CatalogPhoto;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogPhotoManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_replacing_cover_demotes_previous_cover_and_keeps_both_files(): void
    {
        Storage::fake('public');
        config(['catalog.media_disk' => 'public']);
        $admin = User::factory()->superadmin()->create();
        $catalog = Catalog::factory()->create();
        $this->actingAs($admin);

        $this->post(route('superadmin.catalogs.photos.store', $catalog), [
            'type' => 'cover',
            'photos' => [UploadedFile::fake()->image('first.png', 120, 80)],
        ])->assertRedirect(route('superadmin.catalogs.edit', $catalog));
        $first = $catalog->photos()->firstOrFail();

        $this->post(route('superadmin.catalogs.photos.store', $catalog), [
            'type' => 'cover',
            'photos' => [UploadedFile::fake()->image('second.png', 80, 120)],
        ])->assertRedirect(route('superadmin.catalogs.edit', $catalog));

        $this->assertSame('gallery', $first->fresh()->type);
        $this->assertSame(1, $catalog->photos()->where('type', 'cover')->count());
        $this->assertSame(2, $catalog->photos()->count());
        foreach ($catalog->photos()->get() as $photo) {
            Storage::disk('public')->assertExists($photo->file_path);
            $this->assertStringContainsString('/storage/', $photo->url);
        }

        $galleryPhoto = $catalog->photos()->where('type', 'gallery')->firstOrFail();
        $this->get(route('katalog.show', ['type' => $catalog->type, 'slug' => $catalog->slug]))
            ->assertSee('Galeri foto')
            ->assertSee($galleryPhoto->url, false)
            ->assertSee('Foto tambahan');
    }

    public function test_catalog_admin_upload_is_stored_on_their_catalog_and_cannot_delete_another_catalogs_photo(): void
    {
        Storage::fake('public');
        config(['catalog.media_disk' => 'public']);
        $ownCatalog = Catalog::factory()->umkm()->create();
        $otherCatalog = Catalog::factory()->umkm()->create();
        $admin = User::factory()->catalogAdmin($ownCatalog)->create();
        $otherPhoto = $otherCatalog->photos()->create([
            'file_path' => 'catalogs/'.$otherCatalog->id.'/private.png',
            'type' => 'gallery',
        ]);
        Storage::disk('public')->put($otherPhoto->file_path, 'existing image bytes');

        $this->actingAs($admin)->post(route('catalog-admin.catalog.photos.store'), [
            'catalog_id' => $otherCatalog->id,
            'type' => 'location',
            'photos' => [UploadedFile::fake()->image('location.png', 100, 100)],
        ])->assertRedirect(route('catalog-admin.catalog.edit'));

        $this->assertDatabaseHas('catalog_photos', [
            'catalog_id' => $ownCatalog->id,
            'type' => 'location',
        ]);
        $this->assertDatabaseMissing('catalog_photos', [
            'catalog_id' => $otherCatalog->id,
            'type' => 'location',
        ]);

        $this->delete(route('catalog-admin.catalog.photos.destroy', $otherPhoto))
            ->assertNotFound();
        Storage::disk('public')->assertExists($otherPhoto->file_path);
    }

    public function test_upload_rejects_more_than_one_cover_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->superadmin()->create();
        $catalog = Catalog::factory()->create();

        $this->actingAs($admin)->post(route('superadmin.catalogs.photos.store', $catalog), [
            'type' => 'cover',
            'photos' => [
                UploadedFile::fake()->image('one.png'),
                UploadedFile::fake()->image('two.png'),
            ],
        ])->assertInvalid(['photos']);

        $this->assertDatabaseCount('catalog_photos', 0);
    }

    public function test_upload_rejects_executable_files(): void
    {
        Storage::fake('public');
        $admin = User::factory()->superadmin()->create();
        $catalog = Catalog::factory()->create();

        $this->actingAs($admin)->post(route('superadmin.catalogs.photos.store', $catalog), [
            'type' => 'gallery',
            'photos' => [UploadedFile::fake()->create('payload.php', 12, 'application/x-php')],
        ])->assertInvalid(['photos.0']);

        $this->assertDatabaseCount('catalog_photos', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_product_upload_is_saved_and_rendered_on_public_detail(): void
    {
        Storage::fake('public');
        config(['catalog.media_disk' => 'public']);
        $catalog = Catalog::factory()->umkm()->published()->create([
            'slug' => 'produk-dengan-foto',
        ]);
        $admin = User::factory()->catalogAdmin($catalog)->create();

        $this->actingAs($admin)->post(route('catalog-admin.products.store'), [
            'name' => 'Kerajinan Bambu',
            'description' => 'Produk lokal.',
            'price' => 25000,
            'is_available' => '1',
            'photo' => UploadedFile::fake()->image('produk.png', 200, 140),
        ])->assertRedirect(route('catalog-admin.products.index'));

        $product = Product::query()->where('name', 'Kerajinan Bambu')->firstOrFail();
        Storage::disk('public')->assertExists($product->photo);

        $this->get(route('katalog.show', ['type' => 'umkm', 'slug' => $catalog->slug]))
            ->assertSee($product->photo_url, false)
            ->assertSee('Kerajinan Bambu');
    }
}
