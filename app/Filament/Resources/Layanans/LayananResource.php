<?php

namespace App\Filament\Resources\Layanans;

use App\Filament\Resources\Layanans\Pages;
use App\Models\Layanan;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use BackedEnum;

class LayananResource extends Resource
{
    protected static ?string $model = Layanan::class;

    
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationLabel = 'Jenis Layanan';
    protected static ?string $pluralModelLabel = 'Layanan';


    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('nama_layanan')
                    ->label('Nama Layanan')
                    ->required(),

                Forms\Components\TextInput::make('harga_per_kg')
                    ->label('Harga per Kg (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\TextInput::make('estimasi_hari')
                    ->label('Estimasi Selesai (Hari)')
                    ->numeric()
                    ->required(),
            ]);
    }


    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('nama_layanan')
                    ->label('Layanan')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('harga_per_kg')
                    ->label('Harga/Kg')
                    ->money('IDR'),
                \Filament\Tables\Columns\TextColumn::make('estimasi_hari')
                    ->label('Estimasi')
                    ->suffix(' hari'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLayanans::route('/'),
            'create' => Pages\CreateLayanan::route('/create'),
            'edit'   => Pages\EditLayanan::route('/{record}/edit'),
        ];
    }
}