<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function canAccess(): bool
    {
        $user = auth('erp')->user();
        if (! $user) {
            return false;
        }

        return ! $user->hasAnyRole([
            'Pendaftar',
            'Santri',
            'Wali Santri',
            'wali_santri',
            'orang_tua',
            'wali',
        ]);
    }

    public function mount(): void
    {
        $user = auth('erp')->user();
        if (! $user) {
            return;
        }

        if ($user->hasRole('Pendaftar')) {
            $this->redirect(PendaftarDashboard::getUrl());
            return;
        }

        if ($user->hasAnyRole(['Wali Santri', 'wali_santri', 'orang_tua', 'wali'])) {
            $this->redirect(WaliPortal::getUrl());
            return;
        }
    }
}
