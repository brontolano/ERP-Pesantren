<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan dukungan "kantong terpisah" & gating prioritas pada tabungan santri.
 *
 * Aturan bisnis:
 * - SPP bulanan & Ujian adalah tagihan PRIORITAS.
 * - Wali tidak boleh menambah saldo jajan (topup) selama tagihan prioritas belum lunas.
 * - Pengecualian diatur staff pesantren lewat flag `allow_topup_override`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_savings', function (Blueprint $table) {
            if (!Schema::hasColumn('student_savings', 'allow_topup_override')) {
                $table->boolean('allow_topup_override')
                    ->default(false)
                    ->after('is_frozen')
                    ->comment('Jika true, wali boleh topup jajan walau tagihan prioritas belum lunas (diatur staff)');
            }
            if (!Schema::hasColumn('student_savings', 'override_note')) {
                $table->string('override_note', 255)
                    ->nullable()
                    ->after('allow_topup_override')
                    ->comment('Catatan staff terkait pengecualian topup');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_savings', function (Blueprint $table) {
            if (Schema::hasColumn('student_savings', 'override_note')) {
                $table->dropColumn('override_note');
            }
            if (Schema::hasColumn('student_savings', 'allow_topup_override')) {
                $table->dropColumn('allow_topup_override');
            }
        });
    }
};
