<?php

namespace App\Console\Commands;

use App\Models\Master\City;
use App\Models\Master\District;
use App\Models\Master\Province;
use App\Models\Master\Village;
use App\Models\PpdbRegistration;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateWilayahData extends Command
{
    protected $signature = 'wilayah:migrate-data
        {--dry-run : Preview tanpa update}
        {--report : Export unmatched ke CSV}';

    protected $description = 'Migrasi data alamat free-text ke kode wilayah standar';

    private int $matched = 0;
    private int $unmatched = 0;
    private array $unmatchedRecords = [];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — tidak ada data yang diupdate.');
        }

        $this->info('Migrasi data Students...');
        $this->migrateModel(Student::class, $dryRun);

        $this->info('Migrasi data PPDB Registrations...');
        $this->migrateModel(PpdbRegistration::class, $dryRun);

        $this->newLine();
        $this->info("Hasil: {$this->matched} matched, {$this->unmatched} unmatched.");

        if ($this->option('report') && !empty($this->unmatchedRecords)) {
            $this->exportReport();
        }

        return self::SUCCESS;
    }

    private function migrateModel(string $modelClass, bool $dryRun): void
    {
        $query = $modelClass::whereNull('village_code')
            ->whereNotNull('provinsi')
            ->where('provinsi', '!=', '');

        $total = $query->count();
        if ($total === 0) {
            $this->info("  Tidak ada record untuk diproses.");
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(100, function ($records) use ($dryRun, $bar) {
            foreach ($records as $record) {
                $villageCode = $this->findVillageCode($record);

                if ($villageCode) {
                    $this->matched++;
                    if (!$dryRun) {
                        $record->update(['village_code' => $villageCode]);
                    }
                } else {
                    $this->unmatched++;
                    $this->unmatchedRecords[] = [
                        'model' => class_basename($record),
                        'id' => $record->id,
                        'provinsi' => $record->provinsi,
                        'kab_kota' => $record->kab_kota,
                        'kecamatan' => $record->kecamatan,
                        'desa_kelurahan' => $record->desa_kelurahan,
                    ];
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
    }

    private function findVillageCode($record): ?string
    {
        $provinsi = $this->normalize($record->provinsi);
        $kabKota = $this->normalize($record->kab_kota);
        $kecamatan = $this->normalize($record->kecamatan);
        $desa = $this->normalize($record->desa_kelurahan);

        if (!$provinsi) return null;

        $province = Province::whereRaw('LOWER(name) = ?', [$provinsi])->first()
            ?? Province::whereRaw('LOWER(name) LIKE ?', ["%{$provinsi}%"])->first();

        if (!$province) return null;
        if (!$kabKota) return null;

        $city = $province->cities()
            ->whereRaw('LOWER(name) LIKE ?', ["%{$kabKota}%"])
            ->first();

        if (!$city) return null;
        if (!$kecamatan) return null;

        $district = $city->districts()
            ->whereRaw('LOWER(name) LIKE ?', ["%{$kecamatan}%"])
            ->first();

        if (!$district) return null;
        if (!$desa) return null;

        $village = $district->villages()
            ->whereRaw('LOWER(name) LIKE ?', ["%{$desa}%"])
            ->first();

        return $village?->code;
    }

    private function normalize(?string $text): ?string
    {
        if (!$text) return null;
        $text = strtolower(trim($text));
        $text = preg_replace('/^(kabupaten|kota|kab\.|kec\.|kel\.|desa)\s*/i', '', $text);
        return $text ?: null;
    }

    private function exportReport(): void
    {
        $path = storage_path('app/wilayah_unmatched_' . date('Ymd_His') . '.csv');
        $fp = fopen($path, 'w');
        fputcsv($fp, ['model', 'id', 'provinsi', 'kab_kota', 'kecamatan', 'desa_kelurahan']);

        foreach ($this->unmatchedRecords as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);
        $this->info("Report exported: {$path}");
    }
}
