<?php

namespace App\Filament\Pages;

use App\Models\PpdbRegistration;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class PendaftarDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';
    protected static ?string $slug = 'pendaftar-dashboard';
    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;

    protected string $view = 'filament.pages.pendaftar-dashboard';

    public Collection $registrations;
    public int $totalDocs = 0;
    public array $timeline = [];

    public static function canAccess(): bool
    {
        $user = auth('erp')->user();
        return $user && $user->hasRole('Pendaftar');
    }

    public function mount(): void
    {
        $user = auth('erp')->user();
        $this->totalDocs = count(config('spmb.mandatory_documents', []));
        $this->timeline = config('spmb.timeline', []);

        $mandatoryDocs = config('spmb.mandatory_documents', []);

        $this->registrations = $user?->registrations()
            ->with('documents')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (PpdbRegistration $reg) use ($mandatoryDocs) {
                $reg->approved_docs = $reg->documents->where('status', 'approved')->count();
                $uploadedTypes = $reg->documents->pluck('type')->toArray();
                $reg->missing_docs = collect($mandatoryDocs)
                    ->filter(fn ($doc) => !in_array($doc['type'], $uploadedTypes))
                    ->pluck('label')
                    ->toArray();
                $reg->status_label = match ($reg->status) {
                    'pending' => 'Menunggu Verifikasi',
                    'document_review' => 'Review Dokumen',
                    'selection' => 'Proses Seleksi',
                    'lulus' => 'Lulus Seleksi',
                    'cadangan' => 'Cadangan',
                    'rejected' => 'Tidak Lulus',
                    'enrolled' => 'Santri Aktif',
                    default => ucfirst($reg->status),
                };
                $reg->status_color = match ($reg->status) {
                    'lulus', 'enrolled' => 'success',
                    'cadangan', 'selection', 'document_review' => 'warning',
                    'rejected' => 'danger',
                    default => 'gray',
                };
                $reg->doc_pct = $this->totalDocs > 0 ? round(($reg->approved_docs / $this->totalDocs) * 100) : 0;
                return $reg;
            }) ?? collect();
    }
}
