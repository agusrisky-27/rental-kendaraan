<?php
    namespace App\Controller;

    use App\Entity\Pembayaran;
    use App\Entity\Transaksi;

    class PembayaranController extends BaseController {
        public function index(): void {
            $this->auth();
            $data = array_map(
                fn ($pembayaran) => $pembayaran->toArray(),
                $this->em->getRepository(Pembayaran::class)->findAll()
            );
            $this->ok($data);
        }

        public function show(int $id): void {
            $this->auth();
            $pembayaran = $this->em->find(Pembayaran::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $this->ok($pembayaran->toArray());
        }

        public function store(): void {
            $this->auth();
            $b = $this->body();
            $items = isset($b[0]) ? $b : [$b];
            $entities = [];

            foreach ($items as $item) {
                $transaksi = $this->em->find(Transaksi::class, $item['id_transaksi'])
                    ?? $this->fail('Transaksi tidak ada', 404);

                if ($transaksi->getStatus() !== 'aktif') {
                    $this->fail("Transaksi {$item['id_transaksi']} tidak aktif", 422);
                }
                if ((float) $item['jumlah'] < (float) $transaksi->getTotalHarga()) {
                    $this->fail('Jumlah kurang', 422);
                }

                $pembayaran = new Pembayaran();
                $pembayaran->setTransaksi($transaksi);
                $pembayaran->setTanggalBayar(new \DateTime($item['tanggal_bayar']));
                $pembayaran->setJumlah((string) $item['jumlah']);
                $pembayaran->setMetode($item['metode_pembayaran']);
                $pembayaran->setStatus('lunas');

                // Setelah pembayaran, transaksi menunggu proses pengembalian
                $transaksi->setStatus('menunggu_pengembalian');

                // Hitung kembalian (lebih bayar)
                $kembalian = (float) $item['jumlah'] - (float) $transaksi->getTotalHarga();

                $this->em->persist($pembayaran);
                $entities[] = ['pembayaran' => $pembayaran, 'kembalian' => $kembalian];
            }

            $this->em->flush();
            $result = [];
            foreach ($entities as $row) {
                $pembayaran = $row['pembayaran'];
                $this->em->refresh($pembayaran);
                $arr = $pembayaran->toArray();
                $arr['kembalian'] = $row['kembalian'];
                $result[] = $arr;
            }

            $this->ok($result, count($result) . ' pembayaran berhasil', 201);
        }
    }
?>