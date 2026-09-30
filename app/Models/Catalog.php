<?php

namespace App\Models;

use App\Support\ImageUploader;
use App\Support\WhatsAppNumber;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Catalog extends Model
{
    /** @use HasFactory<\Database\Factories\CatalogFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'sub_type',
        'description',
        'owner_name',
        'responsible_person',
        'address',
        'latitude',
        'longitude',
        'email',
        'whatsapp',
        'phone',
        'instagram',
        'tiktok',
        'other_contact',
        'photo',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'draft',
    ];

    /**
     * @return array<string, list<string>>
     */
    public static function subTypeOptions(): array
    {
        return Category::query()
            ->with('subcategories')
            ->get()
            ->mapWithKeys(fn (Category $category): array => [
                $category->slug => $category->subcategories->pluck('name')->all(),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function typeLabels(): array
    {
        return Category::query()->orderBy('sort_order')->orderBy('name')->pluck('name', 'slug')->all();
    }

    /**
     * @return list<string>
     */
    public static function types(): array
    {
        return array_keys(self::typeLabels());
    }

    public static function uniqueSlugFromName(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $base = $base !== '' ? $base : 'katalog';
        $slug = $base;
        $i = 2;

        while (self::query()
            ->when($ignoreId !== null, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->category?->name ?? Str::headline($this->type);
    }

    public function getContactPersonAttribute(): ?string
    {
        return $this->category?->slug === 'umkm' ? $this->owner_name : $this->responsible_person;
    }

    public function getContactPersonLabelAttribute(): string
    {
        return $this->category?->slug === 'umkm' ? 'Pemilik' : 'Penanggung Jawab';
    }

    public function getDetailUrlAttribute(): string
    {
        return route('katalog.show', [
            'type' => $this->type,
            'slug' => $this->slug,
        ]);
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        return WhatsAppNumber::chatUrl($this->whatsapp);
    }

    public function getInstagramUrlAttribute(): ?string
    {
        return $this->profileUrl($this->instagram, 'https://instagram.com/');
    }

    public function getTiktokUrlAttribute(): ?string
    {
        return $this->profileUrl($this->tiktok, 'https://www.tiktok.com/@');
    }

    public function getPhotoUrlAttribute(): string
    {
        $cover = $this->coverPhoto;

        return $cover?->url ?? app(ImageUploader::class)->url($this->photo, 'images/placeholder-catalog.svg');
    }

    /**
     * @return array<string, string>
     */
    public function availableContacts(): array
    {
        $contacts = [];

        if ($this->whatsapp_url) {
            $contacts['WhatsApp'] = $this->whatsapp_url;
        }

        if (filled($this->phone)) {
            $contacts['Telepon'] = 'tel:'.preg_replace('/\s+/', '', $this->phone);
        }

        if (filled($this->email)) {
            $contacts['Email'] = 'mailto:'.$this->email;
        }

        if ($this->instagram_url) {
            $contacts['Instagram'] = $this->instagram_url;
        }

        if ($this->tiktok_url) {
            $contacts['TikTok'] = $this->tiktok_url;
        }

        return $contacts;
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    #[Scope]
    protected function ofType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function admin(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'catalog_admin');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CatalogActivity::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'type', 'slug');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(CatalogPhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function coverPhoto(): HasOne
    {
        return $this->hasOne(CatalogPhoto::class)->where('type', 'cover')->orderBy('sort_order');
    }

    public function rememberSubcategory(): void
    {
        $category = $this->category;

        if ($category !== null && filled($this->sub_type)) {
            $category->subcategories()->firstOrCreate(['name' => $this->sub_type]);
        }
    }

    private function profileUrl(?string $value, string $prefix): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $value = trim($value);

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return $prefix.ltrim($value, '@');
    }
}
