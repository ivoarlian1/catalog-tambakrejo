<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    private const DEFAULT_CATEGORIES = [
        ['name' => 'UMKM', 'slug' => 'umkm', 'sort_order' => 10],
        ['name' => 'Pendidikan', 'slug' => 'education', 'sort_order' => 20],
        ['name' => 'Kesehatan', 'slug' => 'health', 'sort_order' => 30],
        ['name' => 'Fasilitas Umum', 'slug' => 'public_facility', 'sort_order' => 40],
    ];

    private const DEFAULT_SUBCATEGORIES = [
        'umkm' => ['Kuliner', 'Fashion', 'Kerajinan', 'Jasa', 'Perdagangan', 'Lainnya'],
        'education' => ['PAUD', 'TK', 'SD', 'SMP', 'SMA', 'Madrasah', 'TPQ', 'Lainnya'],
        'health' => ['Puskesmas', 'Posyandu', 'Klinik', 'Apotek', 'Praktik', 'Lainnya'],
        'public_facility' => ['Balai Kelurahan', 'Tempat Ibadah', 'Lapangan', 'Taman', 'Pos Keamanan', 'Fasilitas Publik', 'Lainnya'],
    ];

    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('categories')->insert(array_map(
            fn (array $category): array => [...$category, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            self::DEFAULT_CATEGORIES,
        ));

        Schema::table('catalogs', function (Blueprint $table): void {
            $table->string('type')->change();
            $table->foreign('type')->references('slug')->on('categories')->restrictOnDelete();
        });

        Schema::create('catalog_photos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('catalog_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('type')->default('gallery');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['catalog_id', 'type', 'sort_order']);
        });

        Schema::create('category_subcategories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['category_id', 'name']);
        });

        $categoryIds = DB::table('categories')->pluck('id', 'slug');
        foreach (self::DEFAULT_SUBCATEGORIES as $slug => $subcategories) {
            foreach ($subcategories as $name) {
                DB::table('category_subcategories')->insertOrIgnore([
                    'category_id' => $categoryIds[$slug],
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        DB::table('catalogs')
            ->whereNotNull('sub_type')
            ->where('sub_type', '!=', '')
            ->distinct()
            ->get(['type', 'sub_type'])
            ->each(function (object $catalog) use ($categoryIds): void {
                $categoryId = $categoryIds[$catalog->type] ?? null;

                if ($categoryId !== null) {
                    DB::table('category_subcategories')->insertOrIgnore([
                        'category_id' => $categoryId,
                        'name' => $catalog->sub_type,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        DB::table('catalogs')
            ->whereNotNull('photo')
            ->orderBy('id')
            ->get(['id', 'photo', 'created_at', 'updated_at'])
            ->each(function (object $catalog): void {
                DB::table('catalog_photos')->insert([
                    'catalog_id' => $catalog->id,
                    'file_path' => $catalog->photo,
                    'type' => 'cover',
                    'sort_order' => 0,
                    'created_at' => $catalog->created_at ?? now(),
                    'updated_at' => $catalog->updated_at ?? now(),
                ]);
            });
    }

    public function down(): void
    {
        $legacyTypes = array_column(self::DEFAULT_CATEGORIES, 'slug');

        if (DB::table('catalogs')->whereNotIn('type', $legacyTypes)->exists()) {
            throw new RuntimeException('Dynamic categories are in use; migrate catalog types before rolling back this migration.');
        }

        if (DB::table('categories')->whereNotIn('slug', $legacyTypes)->exists()) {
            throw new RuntimeException('Dynamic categories exist; export or migrate them before rolling back this migration.');
        }

        $containsNewPhotos = DB::table('catalog_photos as catalog_photos')
            ->join('catalogs', 'catalogs.id', '=', 'catalog_photos.catalog_id')
            ->where(function ($query): void {
                $query->whereNull('catalogs.photo')
                    ->orWhereColumn('catalog_photos.file_path', '!=', 'catalogs.photo');
            })
            ->exists();

        if ($containsNewPhotos) {
            throw new RuntimeException('Additional catalog photos exist; migrate photo data before rolling back this migration.');
        }

        Schema::dropIfExists('catalog_photos');
        Schema::dropIfExists('category_subcategories');

        Schema::table('catalogs', function (Blueprint $table): void {
            $table->dropForeign(['type']);
            $table->enum('type', array_column(self::DEFAULT_CATEGORIES, 'slug'))->change();
        });

        Schema::dropIfExists('categories');
    }
};
