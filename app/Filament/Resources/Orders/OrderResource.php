<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages;
use App\Models\Order;
use App\Models\Layanan;
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
                        // Ambil langsung dari database, otomatis update kalau ada layanan baru
                        ->options(fn () => Layanan::pluck('nama_layanan', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                            $layanan = Layanan::find($state);
                            if ($layanan) {
                                $berat = $get('berat_kg');
                                if ($berat) {
                                    $set('total_harga', $layanan->harga_per_kg * $berat);
                                }
                                $set('estimasi_selesai', now()->addDays($layanan->estimasi_hari)->format('Y-m-d'));
                            }
                        }),

                    TextInput::make('berat_kg')
                        ->label('Berat (Kg)')
                        ->numeric()
                        ->step(0.1)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                            $layanan = Layanan::find($get('layanan_id'));
                            if ($layanan && $state) {
                                $set('total_harga', $layanan->harga_per_kg * $state);
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

                // Ambil nama layanan dari database
                Tables\Columns\TextColumn::make('layanan_id')
                    ->label('Layanan')
                    ->formatStateUsing(fn ($state) => Layanan::find($state)?->nama_layanan ?? '-'),

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