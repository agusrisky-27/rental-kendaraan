<?php

namespace App\Controller;

use App\Entity\{Pembayaran, Transaksi};


class PembayaranController extends BaseController
{
    public function index(): void
    {
        $this->auth();

        $data = array_map(
            fn($p) => $p->toArray(),
            $this->em->getRepository(Pembayaran::class)->findAll()
        );

        $this->ok($data);
    }



    public function store(): void
    {
        $this->auth();

        $b = $this->body();

        $items = isset($b[0]) ? $b : [$b];


        $entities = [];


        foreach ($items as $item) {


            $t = $this->em->find(
                Transaksi::class,
                $item['id_transaksi']
            ) ?? $this->fail(
                'Transaksi tidak ada',
                404
            );



            if ($t->getStatus() !== 'aktif') {

                $this->fail(
                    "Transaksi {$item['id_transaksi']} tidak aktif",
                    422
                );
            }



            if ((float)$item['jumlah'] < (float)$t->getTotalHarga()) {

                $this->fail(
                    'Jumlah kurang',
                    422
                );
            }



            $pay = new Pembayaran();



            $pay->setTransaksi($t);

            $pay->setTanggalBayar(
                new \DateTime($item['tanggal_bayar'])
            );

            $pay->setJumlah(
                (string)$item['jumlah']
            );

            $pay->setMetode(
                $item['metode_pembayaran']
            );

            $pay->setStatus('lunas');



            // update transaksi
            $t->setStatus('selesai');


            // kendaraan kembali tersedia
            $t->getKendaraan()
                ->setStatus('tersedia');



            $this->em->persist($pay);


            $entities[] = $pay;
        }



        $this->em->flush();



        $result = [];


        foreach ($entities as $pay) {

            $this->em->refresh($pay);

            $result[] = $pay->toArray();
        }



        $this->ok(
            $result,
            count($result) . ' pembayaran berhasil',
            201
        );
    }
}