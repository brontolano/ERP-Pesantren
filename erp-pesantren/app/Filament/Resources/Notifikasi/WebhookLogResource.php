<?php

namespace App\Filament\Resources\Notifikasi;

use App\Models\WebhookLog;
use Filament\Actions;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class WebhookLogResource extends Resource
{
    protected static ?string $model = WebhookLog::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-signal';
    protected static string|\UnitEnum|null $navigationGroup = 'Notifikasi';
    protected static ?string $navigationLabel = 'Log Webhook';
    protected static ?string $modelLabel = 'Log Webhook';
    protected static ?string $pluralModelLabel = 'Log Webhook';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = auth('erp')->user();
        return $user && $user->hasAnyRole(['Superadmin', 'Admin']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    protected static function statusColor(?int $status): string
    {
        if ($status === null || $status === 0) {
            return 'danger';
        }
        return $status >= 200 && $status < 300 ? 'success' : 'danger';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event')
                    ->label('Event')->badge()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('payload.guardian_phone')
                    ->label('Tujuan (WA)')
                    ->placeholder('-')
                    ->getStateUsing(fn (WebhookLog $r) => $r->payload['guardian_phone']
                        ?? $r->payload['phone']
                        ?? $r->payload['recipient']
                        ?? null)
                    ->searchable(),
                Tables\Columns\TextColumn::make('payload.full_name')
                    ->label('Nama')
                    ->placeholder('-')
                    ->getStateUsing(fn (WebhookLog $r) => $r->payload['full_name']
                        ?? $r->payload['student_name']
                        ?? null),
                Tables\Columns\TextColumn::make('http_status')
                    ->label('HTTP')
                    ->badge()
                    ->color(fn ($state) => static::statusColor($state !== null ? (int) $state : null))
                    ->formatStateUsing(fn ($state) => $state === null ? 'GAGAL' : $state),
                Tables\Columns\TextColumn::make('terkirim')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (WebhookLog $r) => ($r->http_status >= 200 && $r->http_status < 300) ? 'Terkirim' : 'Gagal')
                    ->color(fn (string $state) => $state === 'Terkirim' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Waktu Kirim')->dateTime('d M Y H:i:s')->sortable()->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')->dateTime('d M Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Event')
                    ->options([
                        'spmb.registered'         => 'Pendaftaran',
                        'spmb.document.rejected'  => 'Dokumen Ditolak',
                        'spmb.documents.complete' => 'Dokumen Lengkap',
                        'spmb.selection.decided'  => 'Hasil Seleksi',
                        'spmb.payment.confirmed'  => 'Pembayaran',
                        'spmb.broadcast'          => 'Broadcast',
                    ]),
                Tables\Filters\TernaryFilter::make('status_kirim')
                    ->label('Status Kirim')
                    ->placeholder('Semua')
                    ->trueLabel('Terkirim (2xx)')
                    ->falseLabel('Gagal')
                    ->queries(
                        true: fn ($q) => $q->whereBetween('http_status', [200, 299]),
                        false: fn ($q) => $q->where(fn ($q) => $q->whereNull('http_status')->orWhere('http_status', '<', 200)->orWhere('http_status', '>=', 300)),
                    ),
            ])
            ->recordActions([
                Actions\ViewAction::make()->label('Detail'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ringkasan')
                ->schema([
                    TextEntry::make('event')->label('Event')->badge(),
                    TextEntry::make('http_status')
                        ->label('HTTP Status')
                        ->badge()
                        ->color(fn ($state) => static::statusColor($state !== null ? (int) $state : null))
                        ->formatStateUsing(fn ($state) => $state === null ? 'GAGAL (tidak ada respon)' : $state),
                    TextEntry::make('sent_at')->label('Waktu Kirim')->dateTime('d M Y H:i:s')->placeholder('-'),
                    TextEntry::make('created_at')->label('Dibuat')->dateTime('d M Y H:i:s'),
                ])->columns(2),
            Section::make('Payload Dikirim ke n8n')
                ->schema([
                    KeyValueEntry::make('payload')->hiddenLabel(),
                ])->collapsible(),
            Section::make('Respon dari n8n')
                ->schema([
                    TextEntry::make('response_body')
                        ->hiddenLabel()
                        ->placeholder('Tidak ada respon (kemungkinan webhook gagal / URL salah).')
                        ->formatStateUsing(fn ($state) => $state
                            ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                            : null),
                ])->collapsible(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Notifikasi\WebhookLogResource\Pages\ListWebhookLogs::route('/'),
        ];
    }
}
