<?php

namespace App\Models;

use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'photo',
        'is_available',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_available' => true,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.Number::format((float) $this->price, locale: 'id');
    }

    public function getAvailabilityLabelAttribute(): string
    {
        return $this->is_available ? 'Tersedia' : 'Tidak tersedia';
    }

    public function getPhotoUrlAttribute(): string
    {
        return app(ImageUploader::class)->url($this->photo, 'images/placeholder-product.svg');
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }
}
