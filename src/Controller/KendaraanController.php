<?php
namespace App\Controller;

use App\Entity\Kendaraan;

class KendaraanController extends BaseController
{
    public function index(): void
    {
        $this->auth();
        $data = array_map(fn($k) => $k->toArray(), $this->em->getRepository(Kendaraan::class)->findAll());
        $this->ok($data);
    }

    public function show(int $id): void
    {
        $this->auth();
        $k = $this->em->find(Kendaraan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
        $this->ok($k->toArray());
    }

    public function store(): void
    {
        $this->adminOnly();
        $b = $this->body();
        $items = isset($b[0]) ? $b : [$b];

        $entities = [];
        foreach ($items as $item) {
            $k = new Kendaraan();
            $k->setNamaKendaraan($item['nama_kendaraan']);
            $k->setMerk($item['merk']);
            $k->setJenis($item['jenis']);
            $k->setHargaSewa((string) $item['harga_sewa']);
            $k->setStatus('tersedia');
            $this->em->persist($k);
            $entities[] = $k;
        }

        $this->em->flush();

        // Refresh supaya id terisi dari database
        $result = [];
        foreach ($entities as $k) {
            $this->em->refresh($k);
            $result[] = $k->toArray();
        }

        $this->ok($result, count($result) . ' kendaraan ditambahkan', 201);
    }

    public function update(int $id): void
    {
        $this->adminOnly();
        $k = $this->em->find(Kendaraan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
        $b = $this->body();

        if (isset($b['nama_kendaraan'])) $k->setNamaKendaraan($b['nama_kendaraan']);
        if (isset($b['merk']))           $k->setMerk($b['merk']);
        if (isset($b['jenis']))          $k->setJenis($b['jenis']);
        if (isset($b['harga_sewa']))     $k->setHargaSewa((string) $b['harga_sewa']);
        if (isset($b['status']))         $k->setStatus($b['status']);

        $this->em->flush();
        $this->ok($k->toArray(), 'Kendaraan diupdate');
    }

    public function delete(int $id): void
    {
        $this->adminOnly();
        $k = $this->em->find(Kendaraan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
        if ($k->getStatus() === 'disewa') $this->fail('Kendaraan sedang disewa', 409);

        $this->em->remove($k);
        $this->em->flush();
        $this->ok(null, 'Kendaraan dihapus');
    }
}