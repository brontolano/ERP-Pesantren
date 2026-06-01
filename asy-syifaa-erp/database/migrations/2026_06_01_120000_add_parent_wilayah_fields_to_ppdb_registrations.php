<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('ppdb_registrations', 'ayah_province_code')) {
                $table->char('ayah_province_code', 2)->nullable()->after('ayah_alamat');
                $table->char('ayah_city_code', 5)->nullable()->after('ayah_province_code');
                $table->char('ayah_district_code', 8)->nullable()->after('ayah_city_code');
                $table->char('ayah_village_code', 13)->nullable()->after('ayah_district_code');
                $table->string('ayah_kode_pos', 10)->nullable()->after('ayah_village_code');
                $table->index('ayah_village_code');
            }

            if (!Schema::hasColumn('ppdb_registrations', 'ibu_province_code')) {
                $table->char('ibu_province_code', 2)->nullable()->after('ibu_alamat');
                $table->char('ibu_city_code', 5)->nullable()->after('ibu_province_code');
                $table->char('ibu_district_code', 8)->nullable()->after('ibu_city_code');
                $table->char('ibu_village_code', 13)->nullable()->after('ibu_district_code');
                $table->string('ibu_kode_pos', 10)->nullable()->after('ibu_village_code');
                $table->index('ibu_village_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ppdb_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('ppdb_registrations', 'ayah_village_code')) {
                $table->dropIndex(['ayah_village_code']);
                $table->dropColumn(['ayah_province_code', 'ayah_city_code', 'ayah_district_code', 'ayah_village_code', 'ayah_kode_pos']);
            }
            if (Schema::hasColumn('ppdb_registrations', 'ibu_village_code')) {
                $table->dropIndex(['ibu_village_code']);
                $table->dropColumn(['ibu_province_code', 'ibu_city_code', 'ibu_district_code', 'ibu_village_code', 'ibu_kode_pos']);
            }
        });
    }
};

