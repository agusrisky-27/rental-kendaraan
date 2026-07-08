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

        public function update(int $id): void {
            $this->auth();
            $pengembalian = $this->em->find(Pengembalian::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $b = $this->body();

            if (isset($b['id_transaksi'])) {
                $transaksi = $this->em->find(Transaksi::class, $b['id_transaksi'])
                    ?? $this->fail('Transaksi tidak ada', 404);
                $pengembalian->setTransaksi($transaksi);
            }
            if (isset($b['tanggal_kembali'])) {
                $pengembalian->setTanggalKembali(new \DateTime($b['tanggal_kembali']));
            }
            if (isset($b['kondisi_kendaraan'])) {
                $pengembalian->setKondisiKendaraan($b['kondisi_kendaraan']);
            }
            if (array_key_exists('catatan', $b)) {
                $pengembalian->setCatatan($b['catatan']);
            }
            if (isset($b['status'])) {
                $pengembalian->setStatus($b['status']);
            }

            $this->em->flush();
            $this->ok($pengembalian->toArray(), 'Pengembalian diupdate');
        }

        public function delete(int $id): void {
            $this->auth();
            $pengembalian = $this->em->find(Pengembalian::class, $id) ?? $this->fail('Tidak ditemukan', 404);

            $transaksi = $pengembalian->getTransaksi();
            if ($transaksi) {
                $transaksi->setStatus('menunggu_pengembalian');
                $transaksi->getKendaraan()->setStatus('disewa');
            }

            $this->em->remove($pengembalian);
            $this->em->flush();
            $this->ok(null, 'Pengembalian dihapus');
        }
    }
?>