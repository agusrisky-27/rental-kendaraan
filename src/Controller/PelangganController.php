<?php
    namespace App\Controller;

    use App\Entity\Pelanggan;

    class PelangganController extends BaseController
    {
        public function index(): void
        {
            $this->auth();
            $data = array_map(
                fn ($pelanggan) => $pelanggan->toArray(),
                $this->em->getRepository(Pelanggan::class)->findAll()
            );
            $this->ok($data);
        }

        public function show(int $id): void
        {
            $this->auth();
            $pelanggan = $this->em->find(Pelanggan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $this->ok($pelanggan->toArray());
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
                if ($existing) {
                    $this->fail("Nomor identitas '{$item['nomor_identitas']}' sudah terdaftar.", 409);
                }

                $pelanggan = new Pelanggan();
                $pelanggan->setNama($item['nama']);
                $pelanggan->setAlamat($item['alamat']);
                $pelanggan->setNoHp($item['no_hp']);
                $pelanggan->setNomorIdentitas($item['nomor_identitas']);
                $this->em->persist($pelanggan);
                $entities[] = $pelanggan;
            }

            $this->em->flush();

            $result = [];
            foreach ($entities as $pelanggan) {
                $this->em->refresh($pelanggan);
                $result[] = $pelanggan->toArray();
            }

            $this->ok($result, count($result) . ' pelanggan ditambahkan', 201);
        }

        public function update(int $id): void
        {
            $this->auth();
            $pelanggan = $this->em->find(Pelanggan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $b = $this->body();

            if (isset($b['nama'])) {
                $pelanggan->setNama($b['nama']);
            }
            if (isset($b['alamat'])) {
                $pelanggan->setAlamat($b['alamat']);
            }
            if (isset($b['no_hp'])) {
                $pelanggan->setNoHp($b['no_hp']);
            }
            if (isset($b['nomor_identitas'])) {
                $pelanggan->setNomorIdentitas($b['nomor_identitas']);
            }

            $this->em->flush();
            $this->ok($pelanggan->toArray(), 'Pelanggan diupdate');
        }

        public function delete(int $id): void
        {
            $this->auth();
            $pelanggan = $this->em->find(Pelanggan::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $this->em->remove($pelanggan);
            $this->em->flush();
            $this->ok(null, 'Pelanggan dihapus');
        }
    }
?>