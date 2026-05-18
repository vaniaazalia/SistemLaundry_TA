<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
    'kode_order', 'barcode_data',
    'nama_pelanggan', 'no_hp', 'alamat',
    'layanan_id',
    'berat_kg', 'total_harga',
    'tanggal_masuk', 'estimasi_selesai',
    'tanggal_diambil', 'status', 'catatan'
];

    protected $casts = [
        'tanggal_masuk'     => 'date',
        'estimasi_selesai'  => 'date',
        'tanggal_diambil'   => 'date',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}
