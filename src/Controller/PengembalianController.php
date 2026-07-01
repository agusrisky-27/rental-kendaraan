<?php

namespace App\Controller;

use App\Entity\Pengembalian;
use App\Entity\Transaksi;

class PengembalianController extends BaseController
{
    public function index(): void
    {
        $this->auth();
        $data = array_map(
            fn ($pengembalian) => $pengembalian->toArray(),
            $this->em->getRepository(Pengembalian::class)->findAll()
        );
        $this->ok($data);
    }

    public function store(): void
    {
        $this->auth();
        $b = $this->body();

        $transaksi = $this->em->find(Transaksi::class, $b['id_transaksi'] ?? null)
            ?? $this->fail('Transaksi tidak ada', 404);

        $existingReturn = $this->em->getRepository(Pengembalian::class)
            ->findOneBy(['transaksi' => $transaksi]);
        if ($existingReturn) {
            $this->fail('Pengembalian untuk transaksi ini sudah ada', 409);
        }

        if ($transaksi->getStatus() === 'selesai') {
            $this->fail('Kendaraan sudah dikembalikan', 409);
        }

        $pengembalian = new Pengembalian();
        $pengembalian->setTransaksi($transaksi);
        $pengembalian->setTanggalKembali(new \DateTime($b['tanggal_kembali'] ?? date('Y-m-d')));
        $pengembalian->setKondisiKendaraan($b['kondisi_kendaraan'] ?? 'baik');
        $pengembalian->setCatatan($b['catatan'] ?? null);
        $pengembalian->setStatus('selesai');

        $transaksi->setStatus('selesai');
        $transaksi->getKendaraan()->setStatus('tersedia');

        $this->em->persist($pengembalian);
        $this->em->flush();
        $this->em->refresh($pengembalian);

        $this->ok($pengembalian->toArray(), 'Pengembalian kendaraan berhasil', 201);
    }
}
