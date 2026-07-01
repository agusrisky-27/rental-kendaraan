<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pengembalian')]
class Pengembalian
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_pengembalian', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Transaksi::class)]
    #[ORM\JoinColumn(name: 'id_transaksi', referencedColumnName: 'id_transaksi', nullable: false)]
    private Transaksi $transaksi;

    #[ORM\Column(name: 'tanggal_kembali', type: 'date')]
    private \DateTime $tanggalKembali;

    #[ORM\Column(name: 'kondisi_kendaraan', type: 'string')]
    private string $kondisiKendaraan;

    #[ORM\Column(name: 'catatan', type: 'text', nullable: true)]
    private ?string $catatan = null;

    #[ORM\Column(name: 'status', type: 'string')]
    private string $status = 'selesai';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransaksi(): Transaksi
    {
        return $this->transaksi;
    }

    public function getTanggalKembali(): \DateTime
    {
        return $this->tanggalKembali;
    }

    public function getKondisiKendaraan(): string
    {
        return $this->kondisiKendaraan;
    }

    public function getCatatan(): ?string
    {
        return $this->catatan;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setTransaksi(Transaksi $transaksi): void
    {
        $this->transaksi = $transaksi;
    }

    public function setTanggalKembali(\DateTime $tanggalKembali): void
    {
        $this->tanggalKembali = $tanggalKembali;
    }

    public function setKondisiKendaraan(string $kondisiKendaraan): void
    {
        $this->kondisiKendaraan = $kondisiKendaraan;
    }

    public function setCatatan(?string $catatan): void
    {
        $this->catatan = $catatan;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            'id_pengembalian' => $this->id,
            'id_transaksi' => $this->transaksi->getId(),
            'tanggal_kembali' => $this->tanggalKembali->format('Y-m-d'),
            'kondisi_kendaraan' => $this->kondisiKendaraan,
            'catatan' => $this->catatan,
            'status' => $this->status,
        ];
    }
}
