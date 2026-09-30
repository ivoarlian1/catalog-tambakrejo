<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add foreign key from users.catalog_id to catalogs.id.
     * Done in separate migration to avoid circular dependency ordering issues.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('catalog_id')
                ->references('id')
                ->on('catalogs')
                ->nullOnDelete();

            $table->unique('catalog_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['catalog_id']);
            $table->dropForeign(['catalog_id']);
        });
    }
};
