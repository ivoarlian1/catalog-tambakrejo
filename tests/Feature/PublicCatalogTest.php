<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_listing_excludes_draft_catalogs(): void
    {
        $published = Catalog::factory()->published()->create([
            'name' => 'Warung Published Tambakrejo',
            'slug' => 'warung-published-tambakrejo',
            'type' => 'umkm',
        ]);
        $draft = Catalog::factory()->draft()->create([
            'name' => 'Warung Draft Rahasia',
            'slug' => 'warung-draft-rahasia',
            'type' => 'umkm',
        ]);

        $response = $this->get(route('katalog.index'));

        $response->assertSee($published->name);
        $response->assertDontSee($draft->name);
    }

    public function test_public_detail_uses_stable_slug_and_displays_text_coordinates(): void
    {
        $catalog = Catalog::factory()->umkm()->published()->create([
            'name' => 'Warung Bu Siti',
            'slug' => 'warung-bu-siti',
            'latitude' => -6.1234567,
            'longitude' => 110.1234567,
            'other_contact' => "javascript:alert('xss')",
        ]);

        $response = $this->get(route('katalog.show', ['type' => 'umkm', 'slug' => 'warung-bu-siti']));

        $response->assertSee('Warung Bu Siti')
            ->assertSee('-6.1234567, 110.1234567')
            ->assertSee('WhatsApp')
            ->assertSee('https://wa.me/628123456789', false)
            ->assertSee("javascript:alert('xss')")
            ->assertDontSee('href="javascript:alert', false)
            ->assertSee('QR Code katalog');
    }

    public function test_qr_download_is_a_valid_jpeg_for_the_catalog_detail_route(): void
    {
        $catalog = Catalog::factory()->published()->create([
            'type' => 'health',
            'slug' => 'puskesmas-tambakrejo',
        ]);

        $response = $this->get(route('katalog.qr', ['type' => 'health', 'slug' => $catalog->slug]));

        $response->assertOk()
            ->assertHeader('content-type', 'image/jpeg')
            ->assertDownload('qr-puskesmas-tambakrejo.jpg');

        $image = getimagesizefromstring($response->streamedContent());
        $this->assertSame(IMAGETYPE_JPEG, $image[2]);
        $this->assertGreaterThanOrEqual(800, $image[0]);
        $this->assertSame($catalog->detail_url, route('katalog.show', ['type' => $catalog->type, 'slug' => $catalog->slug]));
    }

    public function test_public_detail_does_not_resolve_a_draft_catalog(): void
    {
        $catalog = Catalog::factory()->draft()->create([
            'type' => 'health',
            'slug' => 'draft-health-center',
        ]);

        $this->get(route('katalog.show', ['type' => $catalog->type, 'slug' => $catalog->slug]))
            ->assertNotFound();
    }

    public function test_existing_public_facility_type_with_underscore_remains_a_valid_detail_and_qr_url(): void
    {
        $catalog = Catalog::factory()->published()->create([
            'type' => 'public_facility',
            'slug' => 'balai-tambakrejo-lama',
        ]);

        $this->get(route('katalog.show', ['type' => 'public_facility', 'slug' => $catalog->slug]))
            ->assertOk();
        $this->get(route('katalog.qr', ['type' => 'public_facility', 'slug' => $catalog->slug]))
            ->assertDownload('qr-balai-tambakrejo-lama.jpg');
    }

    public function test_homepage_exposes_catalog_admin_login_and_copyright(): void
    {
        $this->get(route('home'))
            ->assertSee('Login Catalog Admin')
            ->assertSee('© KKN GIAT 17 UNNES');
    }

    public function test_superadmin_can_download_jpeg_and_existing_svg_qr_formats(): void
    {
        $catalog = Catalog::factory()->published()->create([
            'type' => 'umkm',
            'slug' => 'qr-umkm',
        ]);
        $admin = User::factory()->superadmin()->create();

        $this->actingAs($admin)
            ->get(route('superadmin.catalogs.qr.jpg', $catalog))
            ->assertHeader('content-type', 'image/jpeg')
            ->assertDownload('qr-qr-umkm.jpg');

        $this->get(route('superadmin.catalogs.qr.download', $catalog))
            ->assertHeader('content-type', 'image/svg+xml')
            ->assertDownload('qr-qr-umkm.svg');
    }
}
