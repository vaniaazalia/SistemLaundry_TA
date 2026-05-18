<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_order')
                    ->required(),
                TextInput::make('barcode_data')
                    ->required(),
                TextInput::make('pelanggan_id')
                    ->required()
                    ->numeric(),
                TextInput::make('layanan_id')
                    ->required()
                    ->numeric(),
                TextInput::make('berat_kg')
                    ->required()
                    ->numeric(),
                TextInput::make('total_harga')
                    ->required()
                    ->numeric(),
                DatePicker::make('tanggal_masuk')
                    ->required(),
                DatePicker::make('estimasi_selesai')
                    ->required(),
                DatePicker::make('tanggal_diambil'),
                Select::make('status')
                    ->options([
            'Diproses' => 'Diproses',
            'Dicuci' => 'Dicuci',
            'Disetrika' => 'Disetrika',
            'Selesai' => 'Selesai',
            'Sudah Diambil' => 'Sudah diambil',
        ])
                    ->default('Diproses')
                    ->required(),
                Textarea::make('catatan')
                    ->columnSpanFull(),
            ]);
    }
}
