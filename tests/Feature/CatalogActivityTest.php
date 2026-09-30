<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\CatalogActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_admin_can_manage_only_activities_for_their_catalog(): void
    {
        Storage::fake('public');
        $ownCatalog = Catalog::factory()->create();
        $otherCatalog = Catalog::factory()->create();
        $admin = User::factory()->catalogAdmin($ownCatalog)->create();
        $otherActivity = $otherCatalog->activities()->create(['title' => 'Kegiatan lain']);

        $this->actingAs($admin)
            ->post(route('catalog-admin.activities.store'), [
                'title' => 'Bazar kelurahan',
                'description' => 'Dokumentasi bazar.',
                'event_date' => '2026-09-27',
                'catalog_id' => $otherCatalog->id,
                'photo' => UploadedFile::fake()->image('bazar.jpg'),
            ])
            ->assertRedirect(route('catalog-admin.activities.index'));

        $activity = CatalogActivity::query()->where('title', 'Bazar kelurahan')->firstOrFail();
        $this->assertSame($ownCatalog->id, $activity->catalog_id);
        Storage::disk('public')->assertExists($activity->photo);

        $this->actingAs($admin)
            ->get(route('catalog-admin.activities.edit', $otherActivity))
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('catalog-admin.activities.destroy', $otherActivity))
            ->assertForbidden();
    }

    public function test_public_catalog_detail_renders_activity_without_photo(): void
    {
        $catalog = Catalog::factory()->published()->create();
        $catalog->activities()->create([
            'title' => 'Pelatihan warga',
            'description' => 'Kegiatan masyarakat Tambakrejo.',
            'event_date' => '2026-09-26',
        ]);

        $this->get(route('katalog.show', [$catalog->type, $catalog->slug]))
            ->assertOk()
            ->assertSeeText('Berita & Kegiatan')
            ->assertSee('Pelatihan warga')
            ->assertSee('Kegiatan masyarakat Tambakrejo.');
    }
}