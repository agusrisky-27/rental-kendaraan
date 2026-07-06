<?php
    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;

    #[ORM\Entity]
    #[ORM\Table(name: 'transaksi_sewa')]
    class Transaksi {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(name: 'id_transaksi', type: 'integer')]
        private ?int $id = null;

        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
        private User $user;

        #[ORM\ManyToOne(targetEntity: Pelanggan::class)]
        #[ORM\JoinColumn(name: 'id_pelanggan', referencedColumnName: 'id_pelanggan')]
        private Pelanggan $pelanggan;

        #[ORM\ManyToOne(targetEntity: Kendaraan::class)]
        #[ORM\JoinColumn(name: 'id_kendaraan', referencedColumnName: 'id_kendaraan')]
        private Kendaraan $kendaraan;

        #[ORM\Column(name: 'tanggal_sewa', type: 'date')]
        private \DateTime $tanggalSewa;

        #[ORM\Column(name: 'tanggal_kembali', type: 'date')]
        private \DateTime $tanggalKembali;

        #[ORM\Column(name: 'lama_sewa', type: 'integer')]
        private int $lamaSewa;

        #[ORM\Column(name: 'total_harga', type: 'decimal', precision: 14, scale: 2)]
        private string $totalHarga;

        #[ORM\Column(name: 'status', type: 'string')]
        private string $status = 'aktif';

        public function getId(): ?int { 
            return $this->id; 
        }
        public function getUser(): User { 
            return $this->user; 
        }
        public function getPelanggan(): Pelanggan { 
            return $this->pelanggan; 
        }
        public function getKendaraan(): Kendaraan { 
            return $this->kendaraan; 
        }
        public function getTanggalSewa(): \DateTime { 
            return $this->tanggalSewa; 
        }
        public function getTanggalKembali(): \DateTime { 
            return $this->tanggalKembali; 
        }
        public function getLamaSewa(): int { 
            return $this->lamaSewa; 
        }
        public function getTotalHarga(): string { 
            return $this->totalHarga; 
        }
        public function getStatus(): string { 
            return $this->status; 
        }

        public function setUser(User $v) { 
            $this->user = $v; 
        }
        public function setPelanggan(Pelanggan $v) { 
            $this->pelanggan = $v; 
        }
        public function setKendaraan(Kendaraan $v) { 
            $this->kendaraan = $v; 
        }
        public function setTanggalSewa(\DateTime $v) { 
            $this->tanggalSewa = $v; 
        }
        public function setTanggalKembali(\DateTime $v) { 
            $this->tanggalKembali = $v; 
        }
        public function setLamaSewa(int $v) { 
            $this->lamaSewa = $v; 
        }
        public function setTotalHarga(string $v) { 
            $this->totalHarga = $v; 
        }
        public function setStatus(string $v) { 
            $this->status = $v; 
        }

        public function toArray(): array {
            return [
                'id_transaksi' => $this->id,
                'user' => $this->user->toArray(),
                'pelanggan' => $this->pelanggan->toArray(),
                'kendaraan' => $this->kendaraan->toArray(),
                'tanggal_sewa' => $this->tanggalSewa->format('Y-m-d'),
                'tanggal_kembali' => $this->tanggalKembali->format('Y-m-d'),
                'lama_sewa' => $this->lamaSewa,
                'total_harga' => (float) $this->totalHarga,
                'status' => $this->status,
            ];
        }
    }
?>
