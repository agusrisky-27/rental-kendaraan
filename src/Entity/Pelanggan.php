<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pelanggan')]
class Pelanggan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_pelanggan', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'nama', type: 'string')]
    private string $nama;

    #[ORM\Column(name: 'alamat', type: 'text')]
    private string $alamat;

    #[ORM\Column(name: 'no_hp', type: 'string')]
    private string $noHp;

    #[ORM\Column(name: 'nomor_identitas', type: 'string', unique: true)]
    private string $nomorIdentitas;

    public function getId(): ?int              { return $this->id; }
    public function getNama(): string          { return $this->nama; }
    public function getAlamat(): string        { return $this->alamat; }
    public function getNoHp(): string          { return $this->noHp; }
    public function getNomorIdentitas(): string { return $this->nomorIdentitas; }

    public function setNama(string $v)            { $this->nama           = $v; }
    public function setAlamat(string $v)          { $this->alamat         = $v; }
    public function setNoHp(string $v)            { $this->noHp           = $v; }
    public function setNomorIdentitas(string $v)  { $this->nomorIdentitas = $v; }

    public function toArray(): array
    {
        return [
            'id_pelanggan'    => $this->id,
            'nama'            => $this->nama,
            'alamat'          => $this->alamat,
            'no_hp'           => $this->noHp,
            'nomor_identitas' => $this->nomorIdentitas,
        ];
    }
}
