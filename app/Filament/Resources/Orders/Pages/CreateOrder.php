<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
{
    $tanggal = now()->format('Ymd');
    $count   = \App\Models\Order::whereDate('created_at', today())->count() + 1;
    $kode    = 'LDR-' . $tanggal . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

    $data['kode_order'] = $kode;

    // Pakai ID terakhir + 1 supaya tidak duplikat
    $lastId = \App\Models\Order::max('id') ?? 0;
    $data['barcode_data'] = str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);

    return $data;
}
}