<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalogs', function (Blueprint $table): void {
            $table->string('coordinate_format')->default('latitude_longitude');
            $table->decimal('coordinate_x', 18, 7)->nullable();
            $table->decimal('coordinate_y', 18, 7)->nullable();
        });

        DB::table('catalogs')
            ->where(function ($query): void {
                $query->whereNotNull('latitude')->orWhereNotNull('longitude');
            })
            ->update(['coordinate_format' => 'latitude_longitude']);
    }

    public function down(): void
    {
        Schema::table('catalogs', function (Blueprint $table): void {
            $table->dropColumn(['coordinate_format', 'coordinate_x', 'coordinate_y']);
        });
    }
};
