<?php

namespace App\Support;

use App\Models\Catalog;
use App\Models\CatalogPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

class CatalogPhotoManager
{
    public function __construct(private ImageUploader $images) {}

    /**
     * @param list<UploadedFile> $files
     */
    public function store(Catalog $catalog, array $files, string $type): void
    {
        $storedPaths = [];

        try {
            foreach ($files as $file) {
                $storedPaths[] = $this->images->store($file, 'catalogs/'.$catalog->id);
            }

            DB::transaction(function () use ($catalog, $files, $storedPaths, $type): void {
                $lockedCatalog = Catalog::query()->lockForUpdate()->findOrFail($catalog->id);

                if ($type === 'cover') {
                    $lockedCatalog->photos()->where('type', 'cover')->update(['type' => 'gallery']);
                }

                $sortOrder = (int) ($lockedCatalog->photos()->where('type', $type)->max('sort_order') ?? -1) + 1;

                foreach ($files as $index => $file) {
                    $lockedCatalog->photos()->create([
                        'file_path' => $storedPaths[$index],
                        'type' => $type,
                        'sort_order' => $sortOrder + $index,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }
    }

    public function updateType(Catalog $catalog, CatalogPhoto $photo, string $type): void
    {
        DB::transaction(function () use ($catalog, $photo, $type): void {
            $lockedCatalog = Catalog::query()->lockForUpdate()->findOrFail($catalog->id);

            if ($type === 'cover') {
                $lockedCatalog->photos()->where('type', 'cover')->where('id', '!=', $photo->id)->update(['type' => 'gallery']);
            }

            $photo->update([
                'type' => $type,
                'sort_order' => $type === 'cover'
                    ? 0
                    : ((int) ($lockedCatalog->photos()->where('type', $type)->max('sort_order') ?? -1) + 1),
            ]);
        });
    }

    public function delete(CatalogPhoto $photo): void
    {
        $wasCover = $photo->type === 'cover';
        $catalog = $photo->catalog;
        $path = $photo->file_path;

        DB::transaction(function () use ($catalog, $photo, $wasCover): void {
            $lockedCatalog = Catalog::query()->lockForUpdate()->findOrFail($catalog->id);
            $photo->delete();

            if ($wasCover) {
                $nextCover = $lockedCatalog->photos()->where('type', 'gallery')->orderBy('sort_order')->orderBy('id')->first();
                $nextCover?->update(['type' => 'cover', 'sort_order' => 0]);
            }
        });

        $this->images->delete($path);
    }
}
