<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages;
use App\Models\Order;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use BackedEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Data Order';
    protected static ?string $pluralModelLabel = 'Order';
    protected static ?string $slug = 'orders';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Data Pelanggan')
                ->schema([
                    TextInput::make('nama_pelanggan')
                        ->label('Nama Pelanggan')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('no_hp')
                        ->label('Nomor HP')
                        ->tel()
                        ->required()
                        ->maxLength(20),

                    Textarea::make('alamat')
                        ->label('Alamat')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Detail Laundry')
                ->schema([
                    Select::make('layanan_id')
                        ->label('Jenis Layanan')
                        ->options([
                            1 => 'Reguler Cuci Kering - Rp 10.000/kg',
                            2 => 'Express Sehari Jadi - Rp 15.000/kg',
                            3 => 'Cuci Kering Saja - Rp 5.000/kg',
                            4 => 'Setrika Saja - Rp 6.000/kg',
                        ])
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                            $harga = match((int)$state) {
                                1 => 10000,
                                2 => 15000,
                                3 => 5000,
                                4 => 6000,
                                default => 0,
                            };
                            $estimasi = match((int)$state) {
                                1 => 3,
                                2 => 1,
                                3 => 2,
                                4 => 1,
                                default => 1,
                            };
                            $berat = $get('berat_kg');
                            if ($berat) {
                                $set('total_harga', $harga * $berat);
                            }
                            $set('estimasi_selesai', now()->addDays($estimasi)->format('Y-m-d'));
                        }),

                    TextInput::make('berat_kg')
                        ->label('Berat (Kg)')
                        ->numeric()
                        ->step(0.1)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                            $harga = match((int)$get('layanan_id')) {
                                1 => 10000,
                                2 => 15000,
                                3 => 5000,
                                4 => 6000,
                                default => 0,
                            };
                            if ($harga && $state) {
                                $set('total_harga', $harga * $state);
                            }
                        }),

                    TextInput::make('total_harga')
                        ->label('Total Harga')
                        ->prefix('Rp')
                        ->numeric()
                        ->readOnly(),

                    DatePicker::make('tanggal_masuk')
                        ->label('Tanggal Masuk')
                        ->default(now())
                        ->required(),

                    DatePicker::make('estimasi_selesai')
                        ->label('Estimasi Selesai')
                        ->required(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'Diproses'      => 'Diproses',
                            'Dicuci'        => 'Dicuci',
                            'Disetrika'     => 'Disetrika',
                            'Selesai'       => 'Selesai',
                            'Sudah Diambil' => 'Sudah Diambil',
                        ])
                        ->default('Diproses')
                        ->required(),

                    Textarea::make('catatan')
                        ->label('Catatan (opsional)')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(2),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('status', '!=', 'Sudah Diambil'))
            ->columns([
                Tables\Columns\TextColumn::make('kode_order')
                    ->label('Kode Order')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Diproses'      => 'warning',
                        'Dicuci'        => 'info',
                        'Disetrika'     => 'primary',
                        'Selesai'       => 'success',
                        'Sudah Diambil' => 'gray',
                        default         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Masuk')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('estimasi_selesai')
                    ->label('Estimasi')
                    ->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Diproses'      => 'Diproses',
                        'Dicuci'        => 'Dicuci',
                        'Disetrika'     => 'Disetrika',
                        'Selesai'       => 'Selesai',
                        'Sudah Diambil' => 'Sudah Diambil',
                    ]),
            ])
            ->actions([
            \Filament\Actions\Action::make('cetak')
                ->label('Cetak Nota')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn (Order $record) => route('order.nota', $record))
                ->openUrlInNewTab(),
            \Filament\Actions\EditAction::make(),
            \Filament\Actions\DeleteAction::make(),
        ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}