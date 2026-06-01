<?php

namespace App\Filament\Widgets;

use App\Models\PpdbRegistration;
use Filament\Widgets\DoughnutChartWidget;

class SpmbChartWidget extends DoughnutChartWidget
{
    protected ?string $heading = 'Distribusi Status Pendaftar';
    protected ?string $pollingInterval = '60s';
    protected static ?int $sort = 3;
    protected ?string $maxHeight = '280px';

    public static function canView(): bool
    {
        $user = auth('erp')->user();
        return $user && $user->hasAnyRole(['Superadmin', 'Admin', 'Mudir', 'Wakil Mudir', 'Kepala TU', 'Staf TU']);
    }

    protected function getData(): array
    {
        $currentYear = date('Y') . '/' . (date('Y') + 1);
        $query = PpdbRegistration::where('academic_year', $currentYear);

        $statuses = [
            'pending' => ['label' => 'Menunggu', 'color' => '#f59e0b'],
            'document_review' => ['label' => 'Review Dokumen', 'color' => '#3b82f6'],
            'selection' => ['label' => 'Seleksi', 'color' => '#8b5cf6'],
            'lulus' => ['label' => 'Lulus', 'color' => '#10b981'],
            'cadangan' => ['label' => 'Cadangan', 'color' => '#f97316'],
            'rejected' => ['label' => 'Ditolak', 'color' => '#ef4444'],
            'enrolled' => ['label' => 'Terdaftar', 'color' => '#059669'],
        ];

        $data = [];
        $labels = [];
        $colors = [];

        foreach ($statuses as $status => $meta) {
            $count = (clone $query)->where('status', $status)->count();
            if ($count > 0) {
                $data[] = $count;
                $labels[] = $meta['label'] . " ($count)";
                $colors[] = $meta['color'];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
