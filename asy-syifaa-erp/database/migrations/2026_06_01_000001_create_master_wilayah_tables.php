<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_provinces', function (Blueprint $table) {
            $table->char('code', 2)->primary();
            $table->string('name', 100);
            $table->index('name');
        });

        Schema::create('master_cities', function (Blueprint $table) {
            $table->char('code', 5)->primary();
            $table->char('province_code', 2);
            $table->string('name', 100);
            $table->foreign('province_code')->references('code')->on('master_provinces')->cascadeOnDelete();
            $table->index(['province_code', 'name']);
        });

        Schema::create('master_districts', function (Blueprint $table) {
            $table->char('code', 8)->primary();
            $table->char('city_code', 5);
            $table->string('name', 100);
            $table->foreign('city_code')->references('code')->on('master_cities')->cascadeOnDelete();
            $table->index(['city_code', 'name']);
        });

        Schema::create('master_villages', function (Blueprint $table) {
            $table->char('code', 13)->primary();
            $table->char('district_code', 8);
            $table->string('name', 100);
            $table->char('postal_code', 5)->nullable();
            $table->foreign('district_code')->references('code')->on('master_districts')->cascadeOnDelete();
            $table->index(['district_code', 'name']);
            $table->index('postal_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_villages');
        Schema::dropIfExists('master_districts');
        Schema::dropIfExists('master_cities');
        Schema::dropIfExists('master_provinces');
    }
};
