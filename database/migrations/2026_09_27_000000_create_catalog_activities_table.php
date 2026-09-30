<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('catalog_id')->constrained('catalogs')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->date('event_date')->nullable()->index();
            $table->timestamps();
            $table->index('catalog_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_activities');
    }
};