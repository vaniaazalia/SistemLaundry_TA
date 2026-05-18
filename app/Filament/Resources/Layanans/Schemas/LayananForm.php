<?php

namespace App\Filament\Resources\Layanans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LayananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_layanan')
                    ->required(),
                TextInput::make('harga_per_kg')
                    ->required()
                    ->numeric(),
                TextInput::make('estimasi_hari')
                    ->required()
                    ->numeric(),
            ]);
    }
}
