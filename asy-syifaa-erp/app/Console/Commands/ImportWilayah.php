<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportWilayah extends Command
{
    protected $signature = 'wilayah:import
        {wilayah_sql : Path to wilayah.sql (nama wilayah)}
        {kodepos_sql? : Path to wilayah_kodepos.sql (kode pos)}
        {--fresh : Truncate tables before import}';

    protected $description = 'Import data wilayah Indonesia dari SQL dump cahyadsn/wilayah & wilayah_kodepos';

    private int $provinceCount = 0;
    private int $cityCount = 0;
    private int $districtCount = 0;
    private int $villageCount = 0;

    public function handle(): int
    {
        $wilayahPath = $this->argument('wilayah_sql');
        $kodeposPath = $this->argument('kodepos_sql');

        if (!File::exists($wilayahPath)) {
            $this->error("File tidak ditemukan: {$wilayahPath}");
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->info('Truncating existing data...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('master_villages')->truncate();
            DB::table('master_districts')->truncate();
            DB::table('master_cities')->truncate();
            DB::table('master_provinces')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->info('Parsing wilayah.sql...');
        $this->importWilayah($wilayahPath);

        if ($kodeposPath && File::exists($kodeposPath)) {
            $this->info('Importing kode pos...');
            $this->importKodepos($kodeposPath);
        }

        $this->newLine();
        $this->info('Import selesai!');
        $this->table(
            ['Level', 'Jumlah'],
            [
                ['Provinsi', number_format($this->provinceCount)],
                ['Kab/Kota', number_format($this->cityCount)],
                ['Kecamatan', number_format($this->districtCount)],
                ['Desa/Kelurahan', number_format($this->villageCount)],
            ]
        );

        return self::SUCCESS;
    }

    private function importWilayah(string $path): void
    {
        $content = File::get($path);

        preg_match_all("/\('([^']+)','([^']+)'\)/", $content, $matches, PREG_SET_ORDER);

        $provinces = [];
        $cities = [];
        $districts = [];
        $villages = [];

        $bar = $this->output->createProgressBar(count($matches));
        $bar->start();

        foreach ($matches as $match) {
            $kode = $match[1];
            $nama = $match[2];
            $dotCount = substr_count($kode, '.');

            match ($dotCount) {
                0 => $provinces[] = ['code' => $kode, 'name' => $nama],
                1 => $cities[] = ['code' => $kode, 'province_code' => substr($kode, 0, 2), 'name' => $nama],
                2 => $districts[] = ['code' => $kode, 'city_code' => substr($kode, 0, 5), 'name' => $nama],
                3 => $villages[] = ['code' => $kode, 'district_code' => substr($kode, 0, 8), 'name' => $nama, 'postal_code' => null],
                default => null,
            };

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info('Inserting provinces...');
        foreach (array_chunk($provinces, 100) as $chunk) {
            DB::table('master_provinces')->insertOrIgnore($chunk);
        }
        $this->provinceCount = count($provinces);

        $this->info('Inserting cities...');
        foreach (array_chunk($cities, 500) as $chunk) {
            DB::table('master_cities')->insertOrIgnore($chunk);
        }
        $this->cityCount = count($cities);

        $this->info('Inserting districts...');
        foreach (array_chunk($districts, 1000) as $chunk) {
            DB::table('master_districts')->insertOrIgnore($chunk);
        }
        $this->districtCount = count($districts);

        $this->info('Inserting villages...');
        foreach (array_chunk($villages, 1000) as $chunk) {
            DB::table('master_villages')->insertOrIgnore($chunk);
        }
        $this->villageCount = count($villages);
    }

    private function importKodepos(string $path): void
    {
        $content = File::get($path);

        preg_match_all("/\('([^']+)',\s*'([^']+)'\)/", $content, $matches, PREG_SET_ORDER);

        $bar = $this->output->createProgressBar(count($matches));
        $bar->start();

        $updates = [];
        foreach ($matches as $match) {
            $kode = $match[1];
            $kodepos = $match[2];
            $updates[$kode] = $kodepos;

            if (count($updates) >= 1000) {
                $this->batchUpdatePostalCodes($updates);
                $updates = [];
            }
            $bar->advance();
        }

        if (!empty($updates)) {
            $this->batchUpdatePostalCodes($updates);
        }

        $bar->finish();
        $this->newLine();
    }

    private function batchUpdatePostalCodes(array $updates): void
    {
        $cases = '';
        $codes = [];

        foreach ($updates as $kode => $kodepos) {
            $cases .= "WHEN '{$kode}' THEN '{$kodepos}' ";
            $codes[] = "'{$kode}'";
        }

        $codesStr = implode(',', $codes);
        DB::statement("UPDATE master_villages SET postal_code = CASE code {$cases} END WHERE code IN ({$codesStr})");
    }
}
