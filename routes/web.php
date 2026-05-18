<?php

use Illuminate\Support\Facades\Route;
use App\Models\Order;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/order/nota/{order}', function (Order $order) {
    return view('nota', compact('order'));
})->name('order.nota');

Route::get('/scan', function () {
    return view('scan');
})->name('scan');

Route::get('/order/cari/{kode}', function ($kode) {
    $order = Order::where('barcode_data', $kode)->first();
    if (!$order) {
        return response()->json(['error' => 'Order tidak ditemukan'], 404);
    }
    return response()->json($order);
})->name('order.cari');