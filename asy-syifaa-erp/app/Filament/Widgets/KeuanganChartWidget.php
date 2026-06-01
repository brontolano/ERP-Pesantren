<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class KeuanganChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan 6 Bulan Terakhir';
    protected static ?string $pollingInterval = '60s';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '280px';

    public static function canView(): bool
    {
        $user = auth('erp')->user();
        return $user && $user->hasAnyRole(['Superadmin', 'Admin', 'Mudir', 'Wakil Mudir', 'Bendahara']);
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $months = collect();
        $totals = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));
            $totals->push(
                Payment::whereYear('payment_date', $date->year)
                    ->whereMonth('payment_date', $date->month)
                    ->sum('amount')
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $totals->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }
}
