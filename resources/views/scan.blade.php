<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Scan Barcode</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 40px auto; padding: 20px; }
        h2   { text-align: center; color: #333; }
        input {
            width: 100%; padding: 12px; font-size: 16px;
            border: 2px solid #4CAF50; border-radius: 8px;
            text-align: center; outline: none;
            box-sizing: border-box;
        }
        #result { margin-top: 20px; padding: 15px; border-radius: 8px; display: none; }
        .success { background: #e8f5e9; border: 1px solid #4CAF50; }
        .error   { background: #ffebee; border: 1px solid #f44336; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td    { padding: 6px 10px; border-bottom: 1px solid #eee; }
        td:first-child { font-weight: bold; width: 40%; }
        .badge {
            padding: 4px 10px; border-radius: 12px;
            font-size: 12px; font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>🔍 Scan Barcode Laundry</h2>
    <p style="text-align:center; color:#666">Arahkan scanner ke barcode atau ketik kode order</p>

    <input type="text" id="barcodeInput"
           placeholder="Scan atau ketik kode order..."
           autofocus autocomplete="off">

    <div id="result"></div>

    <script>
        const input  = document.getElementById('barcodeInput');
        const result = document.getElementById('result');
        let timer;

        const layananMap = {
            1: 'Reguler Cuci Kering',
            2: 'Express Sehari Jadi',
            3: 'Cuci Kering Saja',
            4: 'Setrika Saja',
        };

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const kode = this.value.trim();
                if (kode.length >= 5) cariOrder(kode);
            }, 300);
        });

        async function cariOrder(kode) {
            try {
                const res  = await fetch(`/order/cari/${kode}`);
                const data = await res.json();

                if (res.ok) {
                    const statusColor = {
                        'Diproses'      : '#ff9800',
                        'Dicuci'        : '#2196f3',
                        'Disetrika'     : '#9c27b0',
                        'Selesai'       : '#4caf50',
                        'Sudah Diambil' : '#9e9e9e'
                    };
                    const warna   = statusColor[data.status] || '#333';
                    const layanan = layananMap[data.layanan_id] || '-';

                    result.className      = 'success';
                    result.style.display  = 'block';
                    result.innerHTML = `
                        <h3>✅ Order Ditemukan</h3>
                        <table>
                            <tr><td>Kode Order</td><td><b>${data.kode_order}</b></td></tr>
                            <tr><td>Pelanggan</td><td>${data.nama_pelanggan}</td></tr>
                            <tr><td>No. HP</td><td>${data.no_hp}</td></tr>
                            <tr><td>Alamat</td><td>${data.alamat ?? '-'}</td></tr>
                            <tr><td>Layanan</td><td>${layanan}</td></tr>
                            <tr><td>Berat</td><td>${data.berat_kg} Kg</td></tr>
                            <tr><td>Total</td><td><b>Rp ${Number(data.total_harga).toLocaleString('id-ID')}</b></td></tr>
                            <tr><td>Status</td><td>
                                <span class="badge" style="background:${warna}; color:white">${data.status}</span>
                            </td></tr>
                        </table>
                        <br>
                        <a href="/admin/orders/${data.id}/edit"
                           style="display:block; text-align:center; padding:10px; background:#4CAF50; color:white; border-radius:6px; text-decoration:none;">
                           ✏️ Update Status Order
                        </a>
                    `;
                } else {
                    result.className     = 'error';
                    result.style.display = 'block';
                    result.innerHTML     = '<h3>❌ Order tidak ditemukan</h3><p>Pastikan kode barcode benar.</p>';
                }
            } catch (e) {
                result.className     = 'error';
                result.style.display = 'block';
                result.innerHTML     = '<h3>❌ Terjadi kesalahan koneksi</h3>';
            }

            input.value = '';
            input.focus();
        }
    </script>
</body>
</html>