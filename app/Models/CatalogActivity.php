<?php

namespace App\Models;

use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogActivity extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'photo', 'event_date'];

    protected function casts(): array
    {
        return ['event_date' => 'date'];
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        return app(ImageUploader::class)->url($this->photo, 'images/placeholder-catalog.svg');
    }
}