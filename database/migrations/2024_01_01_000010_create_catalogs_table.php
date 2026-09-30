<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['umkm', 'education', 'health', 'public_facility']);
            $table->string('sub_type')->nullable();
            $table->text('description')->nullable();
            $table->string('owner_name')->nullable();       // untuk UMKM
            $table->string('responsible_person')->nullable(); // untuk fasilitas
            $table->string('address');
            $table->string('email')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('other_contact')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();

            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogs');
    }
};
