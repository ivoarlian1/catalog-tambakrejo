<?php

namespace Tests\Feature;

use App\Support\ImageUploader;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SupabaseImageStorageTest extends TestCase
{
    public function test_uploaded_images_are_stored_and_served_from_a_public_supabase_bucket(): void
    {
        $this->configureSupabase();
        Http::preventStrayRequests();
        Http::fake([
            'https://tambakrejo.supabase.co/storage/v1/object/info/catalog-images/*' => Http::response([], 200),
            'https://tambakrejo.supabase.co/storage/v1/object/catalog-images' => Http::response([], 200),
            'https://tambakrejo.supabase.co/storage/v1/object/catalog-images/*' => Http::response([], 200),
        ]);

        $images = app(ImageUploader::class);
        $path = $images->store(UploadedFile::fake()->image('catalog.png'), 'catalogs/42');
        $url = $images->url($path, 'images/placeholder-catalog.svg');

        $this->assertSame(
            'https://tambakrejo.supabase.co/storage/v1/object/public/catalog-images/'.$path,
            $url,
        );

        $images->delete($path);

        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && str_starts_with($request->url(), 'https://tambakrejo.supabase.co/storage/v1/object/catalog-images/catalogs/42/')
            && $request->hasHeader('Authorization', 'Bearer test-service-role-key')
            && $request->hasHeader('x-upsert', 'false'));
        Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && str_starts_with($request->url(), 'https://tambakrejo.supabase.co/storage/v1/object/info/catalog-images/catalogs/42/'));
        Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && $request->url() === 'https://tambakrejo.supabase.co/storage/v1/object/catalog-images'
            && in_array($path, $request->data()['prefixes'], true));
    }

    public function test_missing_supabase_image_uses_the_configured_fallback(): void
    {
        $this->configureSupabase();
        Http::preventStrayRequests();
        Http::fake([
            'https://tambakrejo.supabase.co/storage/v1/object/info/catalog-images/*' => Http::response([
                'statusCode' => '404',
                'error' => 'not_found',
                'message' => 'Object not found',
                'code' => 'NoSuchKey',
            ], 400),
        ]);

        $url = app(ImageUploader::class)->url('catalogs/42/missing.png', 'images/placeholder-catalog.svg');

        $this->assertSame(asset('images/placeholder-catalog.svg'), $url);
    }

    public function test_supabase_upload_failures_are_not_silently_ignored(): void
    {
        $this->configureSupabase();
        Http::preventStrayRequests();
        Http::fake([
            'https://tambakrejo.supabase.co/storage/v1/object/catalog-images/*' => Http::response([], 500),
        ]);

        $this->expectException(RequestException::class);

        app(ImageUploader::class)->store(UploadedFile::fake()->image('catalog.png'), 'catalogs/42');
    }

    private function configureSupabase(): void
    {
        config([
            'catalog.media_disk' => 'supabase',
            'catalog.supabase.url' => 'https://tambakrejo.supabase.co',
            'catalog.supabase.service_role_key' => 'test-service-role-key',
            'catalog.supabase.storage_bucket' => 'catalog-images',
        ]);
    }
}
