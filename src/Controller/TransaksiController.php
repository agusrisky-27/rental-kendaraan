<?php
    namespace App\Controller;

    use App\Entity\Kendaraan;
    use App\Entity\Pelanggan;
    use App\Entity\Transaksi;
    use App\Entity\User;

    class TransaksiController extends BaseController{
        public function index(): void{
            $this->auth();
            $data = array_map(
                fn ($transaksi) => $transaksi->toArray(),
                $this->em->getRepository(Transaksi::class)->findAll()
            );
            $this->ok($data);
        }

        public function show(int $id): void{
            $this->auth();
            $transaksi = $this->em->find(Transaksi::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $this->ok($transaksi->toArray());
        }

        public function store(): void{
            $payload = $this->auth();

            $b = $this->body();
            $items = isset($b[0]) ? $b : [$b];
            $user = $this->em->find(User::class, $payload['id'])
                ?? $this->fail('User tidak ada', 404);
            $entities = [];

            foreach ($items as $item) {
                $pelanggan = $this->em->find(Pelanggan::class, $item['id_pelanggan'])
                    ?? $this->fail('Pelanggan tidak ada', 404);

                $kendaraan = $this->em->find(Kendaraan::class, $item['id_kendaraan'])
                    ?? $this->fail('Kendaraan tidak ada', 404);

                if ($kendaraan->getStatus() !== 'tersedia') {
                    $this->fail("Kendaraan '{$kendaraan->getNamaKendaraan()}' tidak tersedia", 409);
                }

                $tglSewa = new \DateTime($item['tanggal_sewa']);
                $tglKembali = new \DateTime($item['tanggal_kembali']);
                $lama = (int) $tglSewa->diff($tglKembali)->days;
                $total = (float) $kendaraan->getHargaSewa() * $lama;

                $transaksi = new Transaksi();
                $transaksi->setUser($user);
                $transaksi->setPelanggan($pelanggan);
                $transaksi->setKendaraan($kendaraan);
                $transaksi->setTanggalSewa($tglSewa);
                $transaksi->setTanggalKembali($tglKembali);
                $transaksi->setLamaSewa($lama);
                $transaksi->setTotalHarga((string) $total);
                $transaksi->setStatus('aktif');

                $kendaraan->setStatus('disewa');

                $this->em->persist($transaksi);
                $entities[] = $transaksi;
            }

            $this->em->flush();
            $result = [];
            foreach ($entities as $transaksi) {
                $this->em->refresh($transaksi);
                $result[] = $transaksi->toArray();
            }

            $this->ok($result, count($result) . ' transaksi dibuat', 201);
        }

        public function update(int $id): void{
            $this->auth();
            $transaksi = $this->em->find(Transaksi::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $b = $this->body();

            if (isset($b['status'])) {
                $transaksi->setStatus($b['status']);
                if (in_array($b['status'], ['selesai', 'dibatalkan'])) {
                    $transaksi->getKendaraan()->setStatus('tersedia');
                }
            }

            $this->em->flush();
            $this->em->refresh($transaksi);

            $this->ok($transaksi->toArray(), 'Transaksi diupdate');
        }

        public function delete(int $id): void{
            $this->auth();
            $transaksi = $this->em->find(Transaksi::class, $id) ?? $this->fail('Tidak ditemukan', 404);

            $transaksi->getKendaraan()->setStatus('tersedia');
            $this->em->remove($transaksi);
            $this->em->flush();
            $this->ok(null, 'Transaksi dihapus');
        }

        public function returnVehicle(int $id): void{
            $this->auth();
            $transaksi = $this->em->find(Transaksi::class, $id) ?? $this->fail('Tidak ditemukan', 404);

            if ($transaksi->getStatus() === 'selesai') {
                $this->ok($transaksi->toArray(), 'Kendaraan sudah dikembalikan');
            }

            $transaksi->setStatus('selesai');
            $transaksi->getKendaraan()->setStatus('tersedia');

            $this->em->flush();
            $this->em->refresh($transaksi);

            $this->ok($transaksi->toArray(), 'Kendaraan dikembalikan');
        }
    }
?>