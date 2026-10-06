<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SupabaseStorage
{
    public function store(UploadedFile $file, string $path): void
    {
        $response = $this->request()
            ->withHeaders([
                'Content-Type' => $file->getMimeType() ?: 'application/octet-stream',
                'x-upsert' => 'false',
            ])
            ->withBody($file->getContent(), $file->getMimeType() ?: 'application/octet-stream')
            ->post($this->objectUrl($path));

        $this->throwUnlessSuccessful($response, 'mengunggah gambar');
    }

    public function delete(string $path): void
    {
        $response = $this->request()->delete($this->objectCollectionUrl(), [
            'prefixes' => [$path],
        ]);

        $this->throwUnlessSuccessful($response, 'menghapus gambar');
    }

    public function exists(string $path): bool
    {
        $response = $this->request()->get($this->objectInfoUrl($path));

        if ($response->status() === 404) {
            return false;
        }

        $this->throwUnlessSuccessful($response, 'memeriksa gambar');

        return true;
    }

    public function url(string $path): string
    {
        [$url, , $bucket] = $this->configuration();

        return $url.'/storage/v1/object/public/'.rawurlencode($bucket).'/'.$this->encodePath($path);
    }

    private function request(): PendingRequest
    {
        [, $serviceRoleKey] = $this->configuration();

        return Http::acceptJson()->withHeaders([
            'apikey' => $serviceRoleKey,
            'Authorization' => 'Bearer '.$serviceRoleKey,
        ]);
    }

    private function objectUrl(string $path): string
    {
        return $this->objectCollectionUrl().'/'.$this->encodePath($path);
    }

    private function objectInfoUrl(string $path): string
    {
        return $this->objectCollectionUrl('/info').'/'.$this->encodePath($path);
    }

    private function objectCollectionUrl(string $suffix = ''): string
    {
        [$url, , $bucket] = $this->configuration();

        return $url.'/storage/v1/object'.$suffix.'/'.rawurlencode($bucket);
    }

    /**
     * @return array{string, string, string}
     */
    private function configuration(): array
    {
        $url = rtrim((string) config('catalog.supabase.url'), '/');
        $serviceRoleKey = trim((string) config('catalog.supabase.service_role_key'));
        $bucket = trim((string) config('catalog.supabase.storage_bucket'));

        if ($url === '' || $serviceRoleKey === '' || $bucket === '') {
            throw new RuntimeException(
                'Supabase Storage belum dikonfigurasi. Isi SUPABASE_URL, SUPABASE_SERVICE_ROLE_KEY, dan SUPABASE_STORAGE_BUCKET.',
            );
        }

        return [$url, $serviceRoleKey, $bucket];
    }

    private function encodePath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', ltrim($path, '/'))));
    }

    private function throwUnlessSuccessful(Response $response, string $operation): void
    {
        $response->throw();

        if (! $response->successful()) {
            throw new RuntimeException("Supabase Storage gagal {$operation}.");
        }
    }
}
