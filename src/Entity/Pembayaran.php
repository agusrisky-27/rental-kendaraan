<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pembayaran')]
class Pembayaran
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_pembayaran', type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Transaksi::class)]
    #[ORM\JoinColumn(name: 'id_transaksi', referencedColumnName: 'id_transaksi')]
    private Transaksi $transaksi;

    #[ORM\Column(name: 'tanggal_bayar', type: 'date')]
    private \DateTime $tanggalBayar;

    #[ORM\Column(name: 'jumlah', type: 'decimal', precision: 14, scale: 2)]
    private string $jumlah;

    #[ORM\Column(name: 'metode', type: 'string')]
    private string $metode;

    #[ORM\Column(name: 'status', type: 'string')]
    private string $status = 'lunas';

    public function getId(): ?int              { return $this->id; }
    public function getTransaksi(): Transaksi  { return $this->transaksi; }
    public function getTanggalBayar(): \DateTime { return $this->tanggalBayar; }
    public function getJumlah(): string        { return $this->jumlah; }
    public function getMetode(): string        { return $this->metode; }
    public function getStatus(): string        { return $this->status; }

    public function setTransaksi(Transaksi $v)    { $this->transaksi    = $v; }
    public function setTanggalBayar(\DateTime $v) { $this->tanggalBayar = $v; }
    public function setJumlah(string $v)          { $this->jumlah       = $v; }
    public function setMetode(string $v)          { $this->metode       = $v; }
    public function setStatus(string $v)          { $this->status       = $v; }

    public function toArray(): array
    {
        return [
            'id_pembayaran' => $this->id,
            'id_transaksi'  => $this->transaksi->getId(),
            'tanggal_bayar' => $this->tanggalBayar->format('Y-m-d'),
            'jumlah'        => (float) $this->jumlah,
            'metode'        => $this->metode,
            'status'        => $this->status,
        ];
    }
}
