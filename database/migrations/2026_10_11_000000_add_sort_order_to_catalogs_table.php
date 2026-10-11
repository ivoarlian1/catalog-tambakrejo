<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0);
        });

        DB::table('catalogs')->update(['sort_order' => DB::raw('id')]);

        Schema::table('catalogs', function (Blueprint $table) {
            $table->unique('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropUnique(['sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
