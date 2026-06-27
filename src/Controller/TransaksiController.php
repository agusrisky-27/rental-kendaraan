<?php
namespace App\Controller;

use App\Entity\{Transaksi, Kendaraan, Pelanggan, User};

class TransaksiController extends BaseController
{
    public function index(): void
    {
        $this->auth();
        $data = array_map(fn($t) => $t->toArray(), $this->em->getRepository(Transaksi::class)->findAll());
        $this->ok($data);
    }

    public function store(): void
    {
        $p = $this->auth();
        $b = $this->body();
        $items = isset($b[0]) ? $b : [$b];

        $user = $this->em->find(User::class, $p['id']) ?? $this->fail('User tidak ada', 404);

        $result = [];
        foreach ($items as $item) {
            $pelanggan = $this->em->find(Pelanggan::class, $item['id_pelanggan']) ?? $this->fail('Pelanggan tidak ada', 404);
            $kendaraan = $this->em->find(Kendaraan::class, $item['id_kendaraan']) ?? $this->fail('Kendaraan tidak ada', 404);

            if ($kendaraan->getStatus() !== 'tersedia') $this->fail("Kendaraan '{$kendaraan->getNama()}' tidak tersedia", 409);

            $tglSewa    = new \DateTime($item['tanggal_sewa']);
            $tglKembali = new \DateTime($item['tanggal_kembali']);
            $lama       = (int) $tglSewa->diff($tglKembali)->days;
            $total      = (float) $kendaraan->getHargaSewa() * $lama;

            $t = new Transaksi();
            $t->setUser($user);
            $t->setPelanggan($pelanggan);
            $t->setKendaraan($kendaraan);
            $t->setTanggalSewa($tglSewa);
            $t->setTanggalKembali($tglKembali);
            $t->setLamaSewa($lama);
            $t->setTotalHarga((string) $total);
            $t->setStatus('aktif');

            $kendaraan->setStatus('disewa');
            $this->em->persist($t);
            $result[] = $t->toArray();
        }

        $this->em->flush();
        $this->ok($result, count($result) . ' transaksi dibuat', 201);
    }

    public function update(int $id): void
    {
        $this->auth();
        $t = $this->em->find(Transaksi::class, $id) ?? $this->fail('Tidak ditemukan', 404);
        $b = $this->body();

        if (isset($b['status'])) {
            $t->setStatus($b['status']);
            if (in_array($b['status'], ['selesai', 'dibatalkan'])) {
                $t->getKendaraan()->setStatus('tersedia');
            }
        }

        $this->em->flush();
        $this->ok($t->toArray(), 'Transaksi diupdate');
    }
}
