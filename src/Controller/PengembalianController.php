<?php

    namespace App\Controller;

    use App\Entity\Pengembalian;
    use App\Entity\Transaksi;

    class PengembalianController extends BaseController {
        public function index(): void {
            $this->auth();

            $data = array_map(
                fn($pengembalian) => $pengembalian->toArray(),
                $this->em->getRepository(Pengembalian::class)->findAll()
            );

            $this->ok($data);
        }

        public function store(): void {
            $this->auth();

            $b = $this->body();

            // Cari transaksi
            $transaksi = $this->em->find(
                Transaksi::class,
                $b['id_transaksi'] ?? null
            ) ?? $this->fail('Transaksi tidak ada', 404);

            // Cek apakah sudah pernah dikembalikan
            $existingReturn = $this->em
                ->getRepository(Pengembalian::class)
                ->findOneBy([
                    'transaksi' => $transaksi
                ]);

            if ($existingReturn) {
                $this->fail('Pengembalian untuk transaksi ini sudah ada', 409);
            }

            // Cek apakah transaksi sudah selesai
            if ($transaksi->getStatus() === 'selesai') {
                $this->fail('Kendaraan sudah dikembalikan', 409);
            }

            // Simpan data pengembalian
            $pengembalian = new Pengembalian();
            $pengembalian->setTransaksi($transaksi);
            $pengembalian->setTanggalKembali(
                new \DateTime($b['tanggal_kembali'] ?? date('Y-m-d'))
            );
            $pengembalian->setKondisiKendaraan(
                $b['kondisi_kendaraan'] ?? 'Baik'
            );
            $pengembalian->setCatatan(
                $b['catatan'] ?? 'Tidak ada'
            );
            $pengembalian->setStatus('selesai');

            // Transaksi selesai
            $transaksi->setStatus('selesai');

            // Ambil kendaraan dari transaksi
            $kendaraan = $transaksi->getKendaraan();

            // Cek kondisi kendaraan
            if (strtolower($pengembalian->getKondisiKendaraan()) === 'rusak') {
                $kendaraan->setStatus('rusak');
            } else {
                $kendaraan->setStatus('tersedia');
            }

            // Simpan ke database
            $this->em->persist($pengembalian);
            $this->em->flush();
            $this->em->refresh($pengembalian);

            $this->ok(
                $pengembalian->toArray(),
                'Pengembalian kendaraan berhasil',
                201
            );
        }
    }
?>