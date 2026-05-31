<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Master\City;
use App\Models\Master\District;
use App\Models\Master\Province;
use App\Models\Master\Village;
use Illuminate\Support\Facades\Cache;

class WilayahController extends Controller
{
    public function provinces()
    {
        $data = Cache::remember('wilayah.provinces', 86400, function () {
            return Province::orderBy('name')->get(['code', 'name']);
        });

        return response()->json(['data' => $data]);
    }

    public function cities(string $provinceCode)
    {
        $data = Cache::remember("wilayah.cities.{$provinceCode}", 86400, function () use ($provinceCode) {
            return City::where('province_code', $provinceCode)
                ->orderBy('name')->get(['code', 'name']);
        });

        return response()->json(['data' => $data]);
    }

    public function districts(string $cityCode)
    {
        $data = Cache::remember("wilayah.districts.{$cityCode}", 86400, function () use ($cityCode) {
            return District::where('city_code', $cityCode)
                ->orderBy('name')->get(['code', 'name']);
        });

        return response()->json(['data' => $data]);
    }

    public function villages(string $districtCode)
    {
        $data = Cache::remember("wilayah.villages.{$districtCode}", 86400, function () use ($districtCode) {
            return Village::where('district_code', $districtCode)
                ->orderBy('name')->get(['code', 'name', 'postal_code']);
        });

        return response()->json(['data' => $data]);
    }

    public function search(string $query)
    {
        $results = Village::where('name', 'like', "%{$query}%")
            ->with('district.city.province')
            ->limit(20)
            ->get()
            ->map(fn ($v) => [
                'village_code' => $v->code,
                'village' => $v->name,
                'district' => $v->district->name,
                'city' => $v->district->city->name,
                'province' => $v->district->city->province->name,
                'postal_code' => $v->postal_code,
            ]);

        return response()->json(['data' => $results]);
    }
}
