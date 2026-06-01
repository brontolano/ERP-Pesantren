<?php

namespace App\Filament\Forms\Components;

use App\Models\Master\City;
use App\Models\Master\District;
use App\Models\Master\Province;
use App\Models\Master\Village;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;

class WilayahSelect
{
    public static function make(string $prefix = '', ?string $lockField = null): array
    {
        $p = $prefix ? "{$prefix}_" : '';
        $disabledWhenLocked = fn (Get $get): bool => $lockField ? filled($get($lockField)) : false;

        return [
            Select::make("{$p}province_code")
                ->label('Provinsi')
                ->options(fn () => Province::orderBy('name')->pluck('name', 'code'))
                ->disabled($disabledWhenLocked)
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function (Set $set) use ($p) {
                    $set("{$p}city_code", null);
                    $set("{$p}district_code", null);
                    $set("{$p}village_code", null);
                    $set("{$p}kode_pos", null);
                }),

            Select::make("{$p}city_code")
                ->label('Kabupaten/Kota')
                ->disabled($disabledWhenLocked)
                ->options(function (Get $get) use ($p) {
                    $province = $get("{$p}province_code");
                    if (!$province) return [];
                    return City::where('province_code', $province)
                        ->orderBy('name')->pluck('name', 'code');
                })
                ->searchable()
                ->live()
                ->afterStateUpdated(function (Set $set) use ($p) {
                    $set("{$p}district_code", null);
                    $set("{$p}village_code", null);
                    $set("{$p}kode_pos", null);
                }),

            Select::make("{$p}district_code")
                ->label('Kecamatan')
                ->disabled($disabledWhenLocked)
                ->options(function (Get $get) use ($p) {
                    $city = $get("{$p}city_code");
                    if (!$city) return [];
                    return District::where('city_code', $city)
                        ->orderBy('name')->pluck('name', 'code');
                })
                ->searchable()
                ->live()
                ->afterStateUpdated(function (Set $set) use ($p) {
                    $set("{$p}village_code", null);
                    $set("{$p}kode_pos", null);
                }),

            Select::make("{$p}village_code")
                ->label('Desa/Kelurahan')
                ->disabled($disabledWhenLocked)
                ->options(function (Get $get) use ($p) {
                    $district = $get("{$p}district_code");
                    if (!$district) return [];
                    return Village::where('district_code', $district)
                        ->orderBy('name')->pluck('name', 'code');
                })
                ->searchable()
                ->live()
                ->afterStateUpdated(function (Set $set, ?string $state) use ($p) {
                    if ($state) {
                        $village = Village::find($state);
                        $set("{$p}kode_pos", $village?->postal_code);
                    } else {
                        $set("{$p}kode_pos", null);
                    }
                }),

            TextInput::make("{$p}kode_pos")
                ->label('Kode Pos')
                ->maxLength(5)
                ->readOnly(fn (Get $get): bool => $lockField ? filled($get($lockField)) : true),
        ];
    }
}
