<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'kendaraan')]
class Kendaraan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_kendaraan', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'nama_kendaraan', type: 'string')]
    private string $namaKendaraan;

    #[ORM\Column(name: 'merk', type: 'string')]
    private string $merk;

    #[ORM\Column(name: 'jenis', type: 'string')]
    private string $jenis;

    #[ORM\Column(name: 'harga_sewa', type: 'decimal', precision: 12, scale: 2)]
    private string $hargaSewa;

    #[ORM\Column(name: 'status', type: 'string')]
    private string $status = 'tersedia';

    public function getId(): ?int              { return $this->id; }
    public function getNamaKendaraan(): string { return $this->namaKendaraan; }
    public function getMerk(): string          { return $this->merk; }
    public function getJenis(): string         { return $this->jenis; }
    public function getHargaSewa(): string     { return $this->hargaSewa; }
    public function getStatus(): string        { return $this->status; }

    public function setNamaKendaraan(string $v): void { $this->namaKendaraan = $v; }
    public function setMerk(string $v): void          { $this->merk          = $v; }
    public function setJenis(string $v): void         { $this->jenis         = $v; }
    public function setHargaSewa(string $v): void     { $this->hargaSewa     = $v; }
    public function setStatus(string $v): void        { $this->status        = $v; }

    public function toArray(): array
    {
        return [
            'id_kendaraan'   => $this->id,
            'nama_kendaraan' => $this->namaKendaraan,
            'merk'           => $this->merk,
            'jenis'          => $this->jenis,
            'harga_sewa'     => (float) $this->hargaSewa,
            'status'         => $this->status,
        ];
    }
}
