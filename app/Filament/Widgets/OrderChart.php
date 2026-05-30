<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrderChart extends ChartWidget
{
    // Membuat grafik mengambil porsi penuh (lebar maksimal) di layar dashboard
   protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Query database murni Laravel untuk mengambil omset per bulan di tahun ini
        $orders = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_harga) as total_income') // Pastikan nama kolom harga sudah benar
        )
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->pluck('total_income', 'month')
        ->toArray();

        // Menyusun data grafik dari Januari (1) sampai Desember (12)
        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = $orders[$m] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (IDR)',
                    'data' => $chartData,
                    'borderColor' => '#db2777', // Warna garis pink premium kustom kamu
                    'backgroundColor' => 'rgba(219, 39, 119, 0.1)', // Gradasi pink di bawah garis
                    'fill' => 'start', 
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    // Menggantikan properti $heading bawaan agar aman dari bentrokan versi Filament
    public function getHeading(): ?string
    {
        return 'Tren Pendapatan Bulanan';
    }

    // Mengatur visual grafik agar pas di layar
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'animation' => false,
            'scales' => [
                'y' => [
                    'grid' => [
                        'display' => true,
                    ],
                ],
            ],
        ];
    }
}