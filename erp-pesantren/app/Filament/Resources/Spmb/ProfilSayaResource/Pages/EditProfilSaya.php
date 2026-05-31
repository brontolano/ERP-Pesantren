<?php

namespace App\Filament\Resources\Spmb\ProfilSayaResource\Pages;

use App\Filament\Resources\Spmb\ProfilSayaResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProfilSaya extends EditRecord
{
    protected static string $resource = ProfilSayaResource::class;

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $requiredFields = [
            'student_name',
            'gender',
            'birth_date',
            'alamat_jalan',
            'parent_name',
            'parent_phone',
            'ayah_nama',
            'ibu_nama',
            'village_code',
        ];

        $isComplete = collect($requiredFields)->every(fn (string $field) => filled($record->{$field}));
        $record->update(['profile_completed_at' => $isComplete ? now() : null]);

        if (!$isComplete) {
            Notification::make()
                ->title('Profil belum lengkap')
                ->body('Lengkapi data wajib agar proses seleksi berjalan lancar.')
                ->warning()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
