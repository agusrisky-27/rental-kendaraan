<?php
namespace App\Controller;

use App\Entity\User;
use Firebase\JWT\JWT;

class UserController extends BaseController
{
    public function register(): void
    {
        $b = $this->body();
        $repo = $this->em->getRepository(User::class);
        if ($repo->findOneBy(['email' => $b['email']])) $this->fail('Email sudah ada', 409);

        $u = new User();
        $u->setNama($b['nama']);
        $u->setEmail($b['email']);
        $u->setPassword(password_hash($b['password'], PASSWORD_BCRYPT));
        $u->setRole($b['role'] ?? 'staff');

        $this->em->persist($u);
        $this->em->flush();
        $this->ok($u->toArray(), 'Register berhasil', 201);
    }

    public function login(): void
    {
        $b = $this->body();
        $u = $this->em->getRepository(User::class)->findOneBy(['email' => $b['email']]);
        if (!$u || !password_verify($b['password'], $u->getPassword())) $this->fail('Email/password salah', 401);

        $token = JWT::encode([
            'id'    => $u->getIdUser(),
            'email' => $u->getEmail(),
            'role'  => $u->getRole(),
            'exp'   => time() + JWT_EXPIRE,
        ], JWT_SECRET, 'HS256');

        $this->ok(['token' => $token, 'user' => $u->toArray()], 'Login berhasil');
    }

    public function index(): void
    {
        $this->adminOnly();
        $users = array_map(fn($u) => $u->toArray(), $this->em->getRepository(User::class)->findAll());
        $this->ok($users);
    }
}