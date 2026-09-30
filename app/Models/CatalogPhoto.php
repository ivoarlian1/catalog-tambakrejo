<?php

namespace App\Models;

use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogPhoto extends Model
{
    protected $fillable = [
        'file_path',
        'type',
        'sort_order',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function getUrlAttribute(): string
    {
        return app(ImageUploader::class)->url($this->file_path, 'images/placeholder-catalog.svg');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'cover' => 'Foto utama',
            'location' => 'Foto lokasi',
            default => 'Foto tambahan',
        };
    }
}
