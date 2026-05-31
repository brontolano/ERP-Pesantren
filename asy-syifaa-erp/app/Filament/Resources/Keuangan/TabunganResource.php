<?php

namespace App\Filament\Resources\Keuangan;

use App\Filament\Resources\Keuangan\TabunganResource\Pages;
use App\Models\StudentSaving;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TabunganResource extends Resource
{
    protected static ?string $model = StudentSaving::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wallet';
    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Tabungan Santri';
    protected static ?string $modelLabel = 'Tabungan Santri';
    protected static ?string $pluralModelLabel = 'Tabungan Santri';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        $user = auth('erp')->user();
        return $user && $user->hasAnyRole(['Superadmin', 'Admin', 'Mudir', 'Wakil Mudir', 'Bendahara', 'Kepala TU']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Placeholder::make('student_name')
                ->label('Santri')
                ->content(fn (?StudentSaving $record) => $record?->student?->full_name ?? '-'),
            Forms\Components\TextInput::make('saldo')
                ->label('Saldo')
                ->numeric()->prefix('Rp')
                ->helperText('Penyesuaian saldo manual dicatat sebagai koreksi.'),
            Forms\Components\TextInput::make('limit_harian')
                ->label('Limit Jajan Harian')
                ->numeric()->prefix('Rp')->default(30000),
            Forms\Components\Toggle::make('is_frozen')
                ->label('Bekukan Tabungan'),
            Forms\Components\Toggle::make('allow_topup_override')
                ->label('Izinkan Setor Saldo Walau Ada Tunggakan Prioritas')
                ->helperText('Pengecualian: wali boleh setor saldo jajan walau SPP/Ujian belum lunas.')
                ->live(),
            Forms\Components\TextInput::make('override_note')
                ->label('Catatan Pengecualian')
                ->maxLength(255)
                ->visible(fn (Get $get) => (bool) $get('allow_topup_override')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Santri')->searchable()->sortable()->placeholder('-'),
                Tables\Columns\TextColumn::make('saldo')
                    ->label('Saldo')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('limit_harian')
                    ->label('Limit Harian')->money('IDR'),
                Tables\Columns\IconColumn::make('is_frozen')
                    ->label('Beku')->boolean(),
                Tables\Columns\IconColumn::make('allow_topup_override')
                    ->label('Override Setor')->boolean(),
                Tables\Columns\TextColumn::make('last_transaction_at')
                    ->label('Transaksi Terakhir')->dateTime('d/m/Y H:i')->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_frozen')->label('Status Beku'),
                Tables\Filters\TernaryFilter::make('allow_topup_override')->label('Override Setor'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTabungan::route('/'),
            'edit'  => Pages\EditTabungan::route('/{record}/edit'),
        ];
    }
}
