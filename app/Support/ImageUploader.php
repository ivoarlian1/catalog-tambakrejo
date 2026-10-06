<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class ImageUploader
{
    /**
     * @var list<string>
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public function store(UploadedFile $file, string $directory): string
    {
        $extension = strtolower((string) $file->extension());

        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new InvalidArgumentException('Ekstensi gambar tidak diizinkan.');
        }

        $filename = Str::uuid()->toString().'.'.$extension;
        $path = trim($directory, '/').'/'.$filename;

        if ($this->disk() === 'supabase') {
            app(SupabaseStorage::class)->store($file, $path);

            return $path;
        }

        $storedPath = $file->storeAs($directory, $filename, $this->disk());

        if (! is_string($storedPath)) {
            throw new RuntimeException('Foto gagal disimpan ke filesystem.');
        }

        return $storedPath;
    }

    public function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if ($this->disk() === 'supabase') {
            app(SupabaseStorage::class)->delete($path);

            return;
        }

        Storage::disk($this->disk())->delete($path);
    }

    public function url(?string $path, string $fallbackAsset): string
    {
        if ($path === null || $path === '') {
            return asset($fallbackAsset);
        }

        if ($this->disk() === 'supabase') {
            $storage = app(SupabaseStorage::class);

            return $storage->exists($path) ? $storage->url($path) : asset($fallbackAsset);
        }

        $disk = Storage::disk($this->disk());

        if (! $disk->exists($path)) {
            return asset($fallbackAsset);
        }

        if ($this->disk() === 'public') {
            return '/storage/'.ltrim($path, '/');
        }

        return $disk->url($path);
    }

    public function disk(): string
    {
        return (string) config('catalog.media_disk', 'public');
    }
}
