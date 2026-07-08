<?php

    use App\Controller\PembayaranController;
    use App\Controller\KendaraanController;
    use App\Controller\PelangganController;
    use App\Controller\PengembalianController;
    use App\Controller\TransaksiController;
    use App\Controller\UserController;
    use Doctrine\ORM\EntityManager;

    function route(string $method, string $path, EntityManager $em): void {
        $path = rtrim($path, '/');

        // Auth
        if ($method === 'POST' && $path === '/api/register') {
            (new UserController($em))->register();
            return;
        }
        if ($method === 'POST' && $path === '/api/login') {
            (new UserController($em))->login();
            return;
        }

        // Users 
        if ($method === 'GET' && $path === '/api/users') {
            (new UserController($em))->index();
            return;
        }
        if ($method === 'GET' && preg_match('#^/api/users/(\d+)$#', $path, $m)) {
            (new UserController($em))->show((int) $m[1]);
            return;
        }
        if ($method === 'PUT' && preg_match('#^/api/users/(\d+)$#', $path, $m)) {
            (new UserController($em))->update((int) $m[1]);
            return;
        }
        if ($method === 'DELETE' && preg_match('#^/api/users/(\d+)$#', $path, $m)) {
            (new UserController($em))->delete((int) $m[1]);
            return;
        }

        // Kendaraan 
        if ($method === 'GET' && $path === '/api/kendaraan') {
            (new KendaraanController($em))->index();
            return;
        }
        if ($method === 'GET' && preg_match('#^/api/kendaraan/(\d+)$#', $path, $m)) {
            (new KendaraanController($em))->show((int) $m[1]);
            return;
        }
        if ($method === 'POST' && $path === '/api/kendaraan') {
            (new KendaraanController($em))->store();
            return;
        }
        if ($method === 'PUT' && preg_match('#^/api/kendaraan/(\d+)$#', $path, $m)) {
            (new KendaraanController($em))->update((int) $m[1]);
            return;
        }
        if ($method === 'DELETE' && preg_match('#^/api/kendaraan/(\d+)$#', $path, $m)) {
            (new KendaraanController($em))->delete((int) $m[1]);
            return;
        }

        // Pelanggan 
        if ($method === 'GET' && $path === '/api/pelanggan') {
            (new PelangganController($em))->index();
            return;
        }
        if ($method === 'GET' && preg_match('#^/api/pelanggan/(\d+)$#', $path, $m)) {
            (new PelangganController($em))->show((int) $m[1]);
            return;
        }
        if ($method === 'POST' && $path === '/api/pelanggan') {
            (new PelangganController($em))->store();
            return;
        }
        if ($method === 'PUT' && preg_match('#^/api/pelanggan/(\d+)$#', $path, $m)) {
            (new PelangganController($em))->update((int) $m[1]);
            return;
        }
        if ($method === 'DELETE' && preg_match('#^/api/pelanggan/(\d+)$#', $path, $m)) {
            (new PelangganController($em))->delete((int) $m[1]);
            return;
        }

        //  Transaksi 
        if ($method === 'GET' && $path === '/api/transaksi') {
            (new TransaksiController($em))->index();
            return;
        }
        if ($method === 'GET' && preg_match('#^/api/transaksi/(\d+)$#', $path, $m)) {
            (new TransaksiController($em))->show((int) $m[1]);
            return;
        }
        if ($method === 'POST' && $path === '/api/transaksi') {
            (new TransaksiController($em))->store();
            return;
        }
        if ($method === 'PUT' && preg_match('#^/api/transaksi/(\d+)$#', $path, $m)) {
            (new TransaksiController($em))->update((int) $m[1]);
            return;
        }
        if ($method === 'DELETE' && preg_match('#^/api/transaksi/(\d+)$#', $path, $m)) {
            (new TransaksiController($em))->delete((int) $m[1]);
            return;
        }
        if (($method === 'POST' || $method === 'PUT') && preg_match('#^/api/transaksi/(\d+)/kembalikan$#', $path, $m)) {
            (new TransaksiController($em))->returnVehicle((int) $m[1]);
            return;
        }

        // Pengembalian 
        if ($method === 'GET' && $path === '/api/pengembalian') {
            (new PengembalianController($em))->index();
            return;
        }
        if ($method === 'POST' && $path === '/api/pengembalian') {
            (new PengembalianController($em))->store();
            return;
        }
        if ($method === 'PUT' && preg_match('#^/api/pengembalian/(\d+)$#', $path, $m)) {
            (new PengembalianController($em))->update((int) $m[1]);
            return;
        }
        if ($method === 'DELETE' && preg_match('#^/api/pengembalian/(\d+)$#', $path, $m)) {
            (new PengembalianController($em))->delete((int) $m[1]);
            return;
        }

        // Pembayaran 
        if ($method === 'GET' && $path === '/api/pembayaran') {
            (new PembayaranController($em))->index();
            return;
        }
        if ($method === 'GET' && preg_match('#^/api/pembayaran/(\d+)$#', $path, $m)) {
            (new PembayaranController($em))->show((int) $m[1]);
            return;
        }
        if ($method === 'POST' && $path === '/api/pembayaran') {
            (new PembayaranController($em))->store();
            return;
        }
        if ($method === 'PUT' && preg_match('#^/api/pembayaran/(\d+)$#', $path, $m)) {
            (new PembayaranController($em))->update((int) $m[1]);
            return;
        }
        if ($method === 'DELETE' && preg_match('#^/api/pembayaran/(\d+)$#', $path, $m)) {
            (new PembayaranController($em))->delete((int) $m[1]);
            return;
        }

        // 404 Not Found 
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => "Route '{$method} {$path}' tidak ditemukan.",
        ]);
    }
?>