<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_order')
                    ->searchable(),
                TextColumn::make('barcode_data')
                    ->searchable(),
                TextColumn::make('pelanggan_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('layanan_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('berat_kg')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_harga')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tanggal_masuk')
                    ->date()
                    ->sortable(),
                TextColumn::make('estimasi_selesai')
                    ->date()
                    ->sortable(),
                TextColumn::make('tanggal_diambil')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
