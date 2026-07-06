<?php
    namespace App\Controller;

    use App\Entity\Kendaraan;

    class KendaraanController extends BaseController {
        public function index(): void {
            $this->auth();
            $data = array_map(
                fn ($kendaraan) => $kendaraan->toArray(),
                $this->em->getRepository(Kendaraan::class)->findAll()
            );
            $this->ok($data);
        }

        public function show(int $id): void {
            $this->auth();
            $kendaraan = $this->em->find(Kendaraan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $this->ok($kendaraan->toArray());
        }

        public function store(): void {
            $this->adminOnly();
            $b = $this->body();
            $items = isset($b[0]) ? $b : [$b];

            $entities = [];
            foreach ($items as $item) {
                $kendaraan = new Kendaraan();
                $kendaraan->setNamaKendaraan($item['nama_kendaraan']);
                $kendaraan->setMerk($item['merk']);
                $kendaraan->setJenis($item['jenis']);
                $kendaraan->setHargaSewa((string) $item['harga_sewa']);
                $kendaraan->setStatus('tersedia');
                $this->em->persist($kendaraan);
                $entities[] = $kendaraan;
            }

            $this->em->flush();

            $result = [];
            foreach ($entities as $kendaraan) {
                $this->em->refresh($kendaraan);
                $result[] = $kendaraan->toArray();
            }

            $this->ok($result, count($result) . ' kendaraan ditambahkan', 201);
        }

        public function update(int $id): void {
            $this->adminOnly();
            $kendaraan = $this->em->find(Kendaraan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $b = $this->body();

            if (isset($b['nama_kendaraan'])) {
                $kendaraan->setNamaKendaraan($b['nama_kendaraan']);
            }
            if (isset($b['merk'])) {
                $kendaraan->setMerk($b['merk']);
            }
            if (isset($b['jenis'])) {
                $kendaraan->setJenis($b['jenis']);
            }
            if (isset($b['harga_sewa'])) {
                $kendaraan->setHargaSewa((string) $b['harga_sewa']);
            }
            if (isset($b['status'])) {
                $kendaraan->setStatus($b['status']);
            }

            $this->em->flush();
            $this->ok($kendaraan->toArray(), 'Kendaraan diupdate');
        }

        public function delete(int $id): void {
            $this->adminOnly();
            $kendaraan = $this->em->find(Kendaraan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            if ($kendaraan->getStatus() === 'disewa') {
                $this->fail('Kendaraan sedang disewa', 409);
            }

            $this->em->remove($kendaraan);
            $this->em->flush();
            $this->ok(null, 'Kendaraan dihapus');
        }
    }
?>