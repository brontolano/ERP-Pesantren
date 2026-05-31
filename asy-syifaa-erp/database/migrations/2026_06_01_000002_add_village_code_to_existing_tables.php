<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->char('province_code', 2)->nullable()->after('kode_pos');
            $table->char('city_code', 5)->nullable()->after('province_code');
            $table->char('district_code', 8)->nullable()->after('city_code');
            $table->char('village_code', 13)->nullable()->after('district_code');
            $table->index('village_code');
            $table->index('province_code');
        });

        Schema::table('ppdb_registrations', function (Blueprint $table) {
            $table->char('province_code', 2)->nullable()->after('kode_pos');
            $table->char('city_code', 5)->nullable()->after('province_code');
            $table->char('district_code', 8)->nullable()->after('city_code');
            $table->char('village_code', 13)->nullable()->after('district_code');
            $table->index('village_code');
            $table->index('province_code');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['village_code']);
            $table->dropIndex(['province_code']);
            $table->dropColumn(['province_code', 'city_code', 'district_code', 'village_code']);
        });

        Schema::table('ppdb_registrations', function (Blueprint $table) {
            $table->dropIndex(['village_code']);
            $table->dropIndex(['province_code']);
            $table->dropColumn(['province_code', 'city_code', 'district_code', 'village_code']);
        });
    }
};
