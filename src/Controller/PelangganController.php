<?php
namespace App\Controller;

use App\Entity\Pelanggan;

class PelangganController extends BaseController
{
    public function index(): void
    {
        $this->auth();
        $data = array_map(fn($p) => $p->toArray(), $this->em->getRepository(Pelanggan::class)->findAll());
        $this->ok($data);
    }

    public function store(): void
    {
        $this->auth();
        $b = $this->body();
        $items = isset($b[0]) ? $b : [$b];

        $entities = [];
        foreach ($items as $item) {
            $existing = $this->em->getRepository(Pelanggan::class)
                ->findOneBy(['nomorIdentitas' => $item['nomor_identitas']]);
            if ($existing) $this->fail("Nomor identitas '{$item['nomor_identitas']}' sudah terdaftar.", 409);

            $p = new Pelanggan();
            $p->setNama($item['nama']);
            $p->setAlamat($item['alamat']);
            $p->setNoHp($item['no_hp']);
            $p->setNomorIdentitas($item['nomor_identitas']);
            $this->em->persist($p);
            $entities[] = $p;
        }

        $this->em->flush();

        $result = [];
        foreach ($entities as $p) {
            $this->em->refresh($p);
            $result[] = $p->toArray();
        }

        $this->ok($result, count($result) . ' pelanggan ditambahkan', 201);
    }

    public function update(int $id): void
    {
        $this->auth();
        $p = $this->em->find(Pelanggan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
        $b = $this->body();

        if (isset($b['nama']))            $p->setNama($b['nama']);
        if (isset($b['alamat']))          $p->setAlamat($b['alamat']);
        if (isset($b['no_hp']))           $p->setNoHp($b['no_hp']);
        if (isset($b['nomor_identitas'])) $p->setNomorIdentitas($b['nomor_identitas']);

        $this->em->flush();
        $this->ok($p->toArray(), 'Pelanggan diupdate');
    }

    public function delete(int $id): void
    {
        $this->auth();
        $p = $this->em->find(Pelanggan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
        $this->em->remove($p);
        $this->em->flush();
        $this->ok(null, 'Pelanggan dihapus');
    }
}