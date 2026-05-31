<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\ClassSchedule;
use App\Models\ErpAccount;
use App\Models\HealthRecord;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SavingsTransaction;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentAttendance;
use App\Models\StudentGrade;
use App\Models\StudentPermission;
use App\Models\StudentSaving;
use App\Models\TahfidzProgress;
use App\Models\TahfidzRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Data dummy untuk akun uji "wali_test".
 *
 * Membuat 2 santri yang ter-link ke akun wali_test (via wali_account_id) beserta
 * seluruh data turunan yang ditampilkan di PWA/Portal Wali: absensi, tahfidz,
 * tabungan, nilai akademik, izin, prestasi, kesehatan, jadwal, dan tagihan.
 *
 * Idempotent: aman dijalankan berulang (keyed by NIS dummy, time-series direset
 * hanya untuk santri dummy ybs). TIDAK menyentuh data santri/akun lain.
 *
 * Jalankan: php artisan db:seed --class=WaliTestDummySeeder
 */
class WaliTestDummySeeder extends Seeder
{
    public function run(): void
    {
        $wali = ErpAccount::where('username', 'wali_test')->first();

        if (! $wali) {
            $this->command?->error('Akun "wali_test" tidak ditemukan. Lewati seeding.');

            return;
        }

        $this->command?->info("Akun wali_test ditemukan (id={$wali->id}). Membuat data dummy...");

        // Periode akademik aktif (default yang dicari akademik(): tahun berjalan + semester 1)
        $tahunAjaran = now()->year.'/'.(now()->year + 1);
        $period = AcademicPeriod::updateOrCreate(
            ['tahun_ajaran' => $tahunAjaran, 'semester' => 1],
            ['label' => "TA {$tahunAjaran} Semester Ganjil", 'is_active' => true],
        );

        $santriDummy = [
            [
                'nis'         => 'DUMMY-2026-001',
                'full_name'   => 'Ahmad Fauzan (Dummy)',
                'jenjang'     => 'MTs',
                'kelas'       => '8',
                'rombel'      => 'VIII-A',
                'birth_date'  => '2011-04-12',
                'gender'      => 'L',
                'juz'         => 5,
                'hadist'      => 40,
                'saldo'       => 400000,   // cukup untuk demo "Bayar dari Saldo" SPP 350k
                'override'    => false,    // topup terkunci: masih ada tunggakan prioritas
                'override_note' => null,
            ],
            [
                'nis'         => 'DUMMY-2026-002',
                'full_name'   => 'Fatimah Azzahra (Dummy)',
                'jenjang'     => 'MA',
                'kelas'       => '11',
                'rombel'      => 'XI-IPA',
                'birth_date'  => '2008-09-03',
                'gender'      => 'P',
                'juz'         => 18,
                'hadist'      => 120,
                'saldo'       => 240000,
                'override'    => true,     // staff memberi pengecualian: boleh setor saldo
                'override_note' => 'Pengecualian disetujui Bendahara — cicilan SPP terpisah.',
            ],
        ];

        foreach ($santriDummy as $d) {
            $student = $this->seedStudent($wali, $d);
            $this->resetTimeSeries($student->id);
            $this->seedAttendance($student->id);
            $this->seedTahfidz($student->id, $d['juz'], $d['hadist']);
            $this->seedSavings($student->id, $d['saldo'], $d['override'], $d['override_note']);
            $this->seedGrades($student->id, $period->id);
            $this->seedPermission($student->id, $wali->id);
            $this->seedAchievement($student->id);
            $this->seedHealth($student->id);
            $this->seedSchedule($student->id);
            $this->seedInvoices($student);

            $this->command?->info("  ✓ Santri '{$d['full_name']}' (id={$student->id}) + data turunan dibuat.");
        }

        $this->command?->info('Selesai. Total santri wali_test: '.$wali->santriAsWali()->count());
    }

    private function seedStudent(ErpAccount $wali, array $d): Student
    {
        return Student::updateOrCreate(
            ['nis' => $d['nis']],
            [
                'full_name'       => $d['full_name'],
                'birth_place'     => 'Sumedang',
                'birth_date'      => $d['birth_date'],
                'gender'          => $d['gender'],
                'kelas'           => $d['kelas'],
                'kelas_detail'    => $d['rombel'],
                'rombel'          => $d['rombel'],
                'jenjang'         => $d['jenjang'],
                'tahun_masuk'     => 2024,
                'status'          => 'aktif',
                'jalur_masuk'     => 'reguler',
                'kebangsaan'      => 'Indonesia',
                'golongan_darah'  => 'O',
                'hobi'            => 'Membaca',
                'cita_cita'       => 'Hafidz Qur\'an',
                'anak_ke'         => 1,
                'jumlah_saudara'  => 2,
                'ayah_nama'       => $wali->full_name,
                'ayah_no_telepon' => $wali->phone ?: '081200000003',
                'ibu_nama'        => 'Siti Aminah (Dummy)',
                'wali_status'     => 'Ayah Kandung',
                'wali_nama'       => $wali->full_name,
                'wali_account_id' => $wali->id,
                'alamat'          => 'Jl. Pesantren No. 1',
                'desa_kelurahan'  => 'Cibeureum',
                'kecamatan'       => 'Cimalaka',
                'kab_kota'        => 'Sumedang',
                'provinsi'        => 'Jawa Barat',
                'kode_pos'        => '45353',
                'spp_amount'      => 350000,
                'adm_amount'      => 50000,
                'tunggakan_bulan' => 1,
                'catatan_umum'    => '[DATA DUMMY — akun uji wali_test]',
            ],
        );
    }

    private function resetTimeSeries(int $sid): void
    {
        StudentAttendance::where('student_id', $sid)->delete();
        TahfidzRecord::where('student_id', $sid)->delete();
        SavingsTransaction::where('student_id', $sid)->delete();
        StudentGrade::where('student_id', $sid)->delete();
        StudentPermission::where('student_id', $sid)->delete();
        StudentAchievement::where('student_id', $sid)->delete();
        HealthRecord::where('student_id', $sid)->delete();
        ClassSchedule::where('student_id', $sid)->delete();
        $invoiceIds = Invoice::withTrashed()->where('student_id_fk', $sid)->pluck('id');
        if ($invoiceIds->isNotEmpty()) {
            InvoiceItem::whereIn('invoice_id', $invoiceIds)->delete();
        }
        Invoice::where('student_id_fk', $sid)->forceDelete();
    }

    private function seedAttendance(int $sid): void
    {
        for ($i = 13; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);
            if ($tanggal->isFriday()) {
                continue; // libur
            }
            $status = 'hadir';
            $kesehatan = 'sehat';
            if ($i === 5) {
                $status = 'sakit';
                $kesehatan = 'sakit_ringan';
            } elseif ($i === 9) {
                $status = 'izin';
            }
            StudentAttendance::create([
                'student_id'       => $sid,
                'tanggal'          => $tanggal->toDateString(),
                'status_kehadiran' => $status,
                'status_kesehatan' => $kesehatan,
                'sesi'             => 'pagi',
                'keterangan'       => $status === 'izin' ? 'Izin keperluan keluarga' : null,
            ]);
        }
    }

    private function seedTahfidz(int $sid, int $juz, int $hadist): void
    {
        TahfidzProgress::updateOrCreate(
            ['student_id' => $sid],
            [
                'total_juz_quran'  => $juz,
                'target_juz_quran' => 30,
                'total_hadist'     => $hadist,
                'target_hadist'    => 300,
                'update_terakhir'  => Carbon::today()->toDateString(),
                'catatan'          => 'Progres stabil, lancar.',
            ],
        );

        $setoran = [
            ['ziyadah', 'quran', 'Al-Baqarah 1-10', 'A'],
            ['murajaah', 'quran', 'Al-Baqarah 11-20', 'B'],
            ['ziyadah', 'hadist', 'Arbain Nawawi 1-3', 'A'],
            ['tasmi', 'quran', 'Juz 30 lengkap', 'A'],
            ['murajaah', 'mufrodat', 'Bab Lingkungan', 'B'],
        ];
        foreach ($setoran as $idx => [$jenis, $kategori, $pencapaian, $nilai]) {
            TahfidzRecord::create([
                'student_id'    => $sid,
                'tanggal_setor' => Carbon::today()->subDays($idx * 2)->toDateString(),
                'kategori'      => $kategori,
                'pencapaian'    => $pencapaian,
                'jenis_setor'   => $jenis,
                'nilai'         => $nilai,
                'catatan_ustadz'=> 'Baik, pertahankan.',
            ]);
        }
    }

    private function seedSavings(int $sid, int $saldo, bool $override = false, ?string $note = null): void
    {
        StudentSaving::updateOrCreate(
            ['student_id' => $sid],
            [
                'saldo'                => $saldo,
                'limit_harian'         => 30000,
                'is_frozen'            => false,
                'allow_topup_override' => $override,
                'override_note'        => $note,
                'last_transaction_at'  => now(),
            ],
        );

        $running = 0;
        $trx = [
            ['kredit', 'setor_wali', 200000, 'Setoran wali (transfer)'],
            ['debit', 'jajan', 15000, 'Jajan kantin'],
            ['debit', 'jajan', 20000, 'Beli alat tulis'],
            ['debit', 'bayar_tagihan', 50000, 'Bayar tagihan dari saldo'],
            ['kredit', 'setor_wali', 100000, 'Setoran wali (transfer)'],
        ];
        // hitung mundur agar saldo akhir = $saldo
        $totalDelta = 0;
        foreach ($trx as [$jenis, , $nominal]) {
            $totalDelta += $jenis === 'kredit' ? $nominal : -$nominal;
        }
        $running = $saldo - $totalDelta;
        foreach ($trx as $idx => [$jenis, $kategori, $nominal, $ket]) {
            $running += $jenis === 'kredit' ? $nominal : -$nominal;
            SavingsTransaction::create([
                'student_id'    => $sid,
                'jenis'         => $jenis,
                'kategori'      => $kategori,
                'nominal'       => $nominal,
                'saldo_sesudah' => $running,
                'keterangan'    => $ket,
                'created_at'    => Carbon::today()->subDays((count($trx) - $idx)),
                'updated_at'    => Carbon::today()->subDays((count($trx) - $idx)),
            ]);
        }
    }

    private function seedGrades(int $sid, int $periodId): void
    {
        $mapel = [
            ['Al-Qur\'an Hadist', 88, 90, 87, 88, 'A'],
            ['Fiqih', 85, 82, 80, 82, 'B'],
            ['Bahasa Arab', 90, 88, 92, 90, 'A'],
            ['Matematika', 78, 75, 80, 78, 'B'],
            ['Bahasa Indonesia', 86, 84, 85, 85, 'A'],
        ];
        foreach ($mapel as $i => [$nama, $harian, $uts, $uas, $akhir, $predikat]) {
            StudentGrade::create([
                'student_id'        => $sid,
                'period_id'         => $periodId,
                'mata_pelajaran'    => $nama,
                'kkm'               => 75,
                'nilai_harian'      => $harian,
                'nilai_uts'         => $uts,
                'nilai_uas'         => $uas,
                'nilai_akhir'       => $akhir,
                'predikat'          => $predikat,
                'ranking_kelas'     => $i === 0 ? 3 : null,
                'total_siswa_kelas' => 28,
            ]);
        }
    }

    private function seedPermission(int $sid, int $waliId): void
    {
        StudentPermission::create([
            'student_id'      => $sid,
            'pengaju_wali_id' => $waliId,
            'jenis_izin'      => 'pulang',
            'tanggal_mulai'   => Carbon::today()->subDays(7)->toDateString(),
            'tanggal_selesai' => Carbon::today()->subDays(5)->toDateString(),
            'alasan'          => 'Acara keluarga',
            'tujuan'          => 'Rumah orang tua',
            'status'          => 'selesai',
            'catatan_staff'   => 'Disetujui.',
        ]);
    }

    private function seedAchievement(int $sid): void
    {
        StudentAchievement::create([
            'student_id'   => $sid,
            'judul'        => 'Juara 2 MHQ Antar Pesantren',
            'kategori'     => 'akademik',
            'tingkat'      => 'kabupaten',
            'peringkat'    => 'Juara 2',
            'penyelenggara'=> 'Kemenag Sumedang',
            'tanggal'      => Carbon::today()->subMonths(2)->toDateString(),
            'keterangan'   => 'Musabaqah Hifzhil Qur\'an 5 Juz',
            'is_published' => true,
        ]);
    }

    private function seedHealth(int $sid): void
    {
        HealthRecord::create([
            'student_id'   => $sid,
            'tanggal'      => Carbon::today()->subDays(5)->toDateString(),
            'keluhan'      => 'Demam ringan',
            'diagnosa'     => 'Flu biasa',
            'penanganan'   => 'Istirahat & obat penurun panas',
            'obat'         => 'Paracetamol',
            'suhu_tubuh'   => 37.8,
            'berat_badan'  => 45.5,
            'tinggi_badan' => 152,
            'catatan'      => 'Sudah membaik.',
        ]);
    }

    private function seedSchedule(int $sid): void
    {
        $jadwal = [
            [1, '07:00', '08:30', 'Al-Qur\'an Hadist', 'Ust. Abdullah'],
            [1, '08:30', '10:00', 'Bahasa Arab', 'Ust. Hamid'],
            [2, '07:00', '08:30', 'Fiqih', 'Ust. Yusuf'],
            [3, '07:00', '08:30', 'Matematika', 'Ust. Rahman'],
            [4, '07:00', '08:30', 'Bahasa Indonesia', 'Ustzh. Aisyah'],
            [5, '07:00', '08:30', 'Tahfidz', 'Ust. Abdullah'],
        ];
        foreach ($jadwal as [$hari, $mulai, $selesai, $mapel, $guru]) {
            ClassSchedule::create([
                'student_id'     => $sid,
                'hari'           => $hari,
                'jam_mulai'      => $mulai,
                'jam_selesai'    => $selesai,
                'mata_pelajaran' => $mapel,
                'guru'           => $guru,
                'ruang'          => 'R-'.$hari,
                'is_active'      => true,
            ]);
        }
    }

    private function seedInvoices(Student $student): void
    {
        $sid = $student->id;

        // 1) SPP berjalan — PRIORITAS, belum lunas
        $spp = Invoice::create([
            'invoice_number' => 'DUMMY-INV-'.$sid.'-01',
            'student_name'   => $student->full_name,
            'student_id_fk'  => $sid,
            'invoice_type'   => 'spp',
            'hijri_label'    => 'SPP Bulan Berjalan',
            'total_amount'   => 350000,
            'paid_amount'    => 0,
            'status'         => 'issued',
            'issued_at'      => Carbon::today()->subDays(10),
            'due_date'       => Carbon::today()->addDays(5)->toDateString(),
        ]);
        InvoiceItem::create(['invoice_id' => $spp->id, 'description' => 'SPP Bulanan', 'amount' => 300000]);
        InvoiceItem::create(['invoice_id' => $spp->id, 'description' => 'Iuran Kegiatan', 'amount' => 50000]);

        // 2) Biaya Ujian — PRIORITAS, belum lunas
        $ujian = Invoice::create([
            'invoice_number' => 'DUMMY-INV-'.$sid.'-02',
            'student_name'   => $student->full_name,
            'student_id_fk'  => $sid,
            'invoice_type'   => 'ujian',
            'hijri_label'    => 'Biaya Ujian Semester',
            'total_amount'   => 150000,
            'paid_amount'    => 0,
            'status'         => 'issued',
            'issued_at'      => Carbon::today()->subDays(7),
            'due_date'       => Carbon::today()->addDays(10)->toDateString(),
        ]);
        InvoiceItem::create(['invoice_id' => $ujian->id, 'description' => 'Biaya Ujian Akhir Semester', 'amount' => 150000]);

        // 3) Seragam — non-prioritas, sebagian terbayar
        $seragam = Invoice::create([
            'invoice_number' => 'DUMMY-INV-'.$sid.'-03',
            'student_name'   => $student->full_name,
            'student_id_fk'  => $sid,
            'invoice_type'   => 'seragam',
            'hijri_label'    => 'Seragam Sekolah',
            'total_amount'   => 250000,
            'paid_amount'    => 100000,
            'status'         => 'partial',
            'issued_at'      => Carbon::today()->subDays(20),
            'due_date'       => Carbon::today()->addDays(3)->toDateString(),
        ]);
        InvoiceItem::create(['invoice_id' => $seragam->id, 'description' => 'Seragam Batik', 'amount' => 150000]);
        InvoiceItem::create(['invoice_id' => $seragam->id, 'description' => 'Seragam Olahraga', 'amount' => 100000]);

        // 4) SPP bulan lalu — sudah lunas
        $lunas = Invoice::create([
            'invoice_number' => 'DUMMY-INV-'.$sid.'-04',
            'student_name'   => $student->full_name,
            'student_id_fk'  => $sid,
            'invoice_type'   => 'spp',
            'hijri_label'    => 'SPP Bulan Lalu',
            'total_amount'   => 350000,
            'paid_amount'    => 350000,
            'status'         => 'paid',
            'issued_at'      => Carbon::today()->subMonth(),
            'due_date'       => Carbon::today()->subDays(20)->toDateString(),
        ]);
        InvoiceItem::create(['invoice_id' => $lunas->id, 'description' => 'SPP Bulanan', 'amount' => 350000]);
    }
}
