<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Notifications\Notification;

class Riwayat extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Riwayat Transaksi';
    protected static ?string $title = 'Riwayat Transaksi';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.riwayat';

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('hapus_semua')
                ->label('Hapus Semua Riwayat')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Hapus Semua Riwayat')
                ->modalDescription('Yakin mau hapus SEMUA riwayat transaksi? Data tidak bisa dikembalikan!')
                ->modalSubmitActionLabel('Ya, Hapus Semua')
                ->action(function () {
                    Order::where('status', 'Sudah Diambil')->delete();
                    Notification::make()
                        ->title('Semua riwayat berhasil dihapus!')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->where('status', 'Sudah Diambil')->orderByDesc('updated_at'))
            ->columns([
                Tables\Columns\TextColumn::make('kode_order')
                    ->label('Kode Order')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No HP'),

                Tables\Columns\TextColumn::make('layanan_id')
                    ->label('Layanan')
                    ->formatStateUsing(fn ($state) => match((int)$state) {
                        1 => 'Reguler Cuci Kering',
                        2 => 'Express Sehari Jadi',
                        3 => 'Cuci Kering Saja',
                        4 => 'Setrika Saja',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('berat_kg')
                    ->label('Berat')
                    ->suffix(' kg'),

                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tgl Masuk')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tgl Diambil')
                    ->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn ($query) => $query->whereMonth('updated_at', now()->month)),
            ])
            ->actions([
                \Filament\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Order $record) => route('order.nota', $record))
                    ->openUrlInNewTab(),

                \Filament\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Riwayat')
                    ->modalDescription('Yakin mau hapus riwayat order ini? Data tidak bisa dikembalikan.')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ]);
    }
}