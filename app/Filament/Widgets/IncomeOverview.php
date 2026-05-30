<?php

namespace App\Filament\Widgets;

use App\Models\Order; // Pastikan namespace model Order kamu sudah benar
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IncomeOverview extends BaseWidget
{
    // Mengatur agar widget otomatis diperbarui (opsional, misal tiap 10 detik)
    protected ?string $pollingInterval = '10s'; 

    protected function getStats(): array
    {
        // 1. Hitung total omset hari ini (status asumi: Lunas / Sukses)
        // Sesuaikan nama kolom status dan tanggal dengan database-mu
        $omsetHariIni = Order::whereDate('created_at', today())->sum('total_harga');

        // 2. Hitung jumlah cucian yang saat ini sedang diproses
        $cucianDiproses = Order::where('status', 'diproses')->count();

        // 3. Hitung jumlah cucian yang sudah selesai tapi belum diambil
        $siapDiambil = Order::where('status', 'selesai')->count();

        return [
            Stat::make('Omset Hari Ini', 'IDR ' . number_format($omsetHariIni, 0, ',', '.'))
                ->description('Total pendapatan masuk hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pesanan Diproses', $cucianDiproses . ' Order')
                ->description('Antrean cucian di mesin/setrika')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('Siap Diambil', $siapDiambil . ' Order')
                ->description('Cucian selesai & menunggu pelanggan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
        ];
    }
}
