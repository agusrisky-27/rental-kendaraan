<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_user', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'nama', type: 'string')]
    private string $nama;

    #[ORM\Column(name: 'email', type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(name: 'password', type: 'string')]
    private string $password;

    #[ORM\Column(name: 'role', type: 'string')]
    private string $role = 'staff';

    public function getId(): ?int      { return $this->id; }
    public function getIdUser(): ?int  { return $this->id; }
    public function getNama(): string  { return $this->nama; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getRole(): string  { return $this->role; }

    public function setNama(string $v)     { $this->nama     = $v; }
    public function setEmail(string $v)    { $this->email    = $v; }
    public function setPassword(string $v) { $this->password = $v; }
    public function setRole(string $v)     { $this->role     = $v; }

    public function toArray(): array
    {
        return [
            'id_user' => $this->id,
            'nama'    => $this->nama,
            'email'   => $this->email,
            'role'    => $this->role,
        ];
    }
}
