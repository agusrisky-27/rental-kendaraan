<?php
    namespace App\Controller;

    use App\Entity\User;
    use Firebase\JWT\JWT;

    class UserController extends BaseController {
        public function register(): void {
            $b = $this->body();
            $repo = $this->em->getRepository(User::class);
            if ($repo->findOneBy(['email' => $b['email']])) {
                $this->fail('Email sudah ada', 409);
            }

            $user = new User();
            $user->setNama($b['nama']);
            $user->setEmail($b['email']);
            $user->setPassword(password_hash($b['password'], PASSWORD_BCRYPT));
            $user->setRole($b['role'] ?? 'staff');

            $this->em->persist($user);
            $this->em->flush();
            $this->ok($user->toArray(), 'Register berhasil', 201);
        }

        public function login(): void {
            $b = $this->body();
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $b['email']]);
            if (!$user || !password_verify($b['password'], $user->getPassword())) {
                $this->fail('Email/password salah', 401);
            }

            $token = JWT::encode(
                [
                    'id' => $user->getIdUser(),
                    'email' => $user->getEmail(),
                    'role' => $user->getRole(),
                    'exp' => time() + JWT_EXPIRE,
                ],
                JWT_SECRET,
                'HS256'
            );

            $this->ok(['token' => $token, 'user' => $user->toArray()], 'Login berhasil');
        }

        public function index(): void {
            $this->adminOnly();
            $users = array_map(
                fn ($user) => $user->toArray(),
                $this->em->getRepository(User::class)->findAll()
            );
            $this->ok($users);
        }

        public function show(int $id): void {
            $this->adminOnly();
            $user = $this->em->find(User::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $this->ok($user->toArray());
        }

        public function update(int $id): void {
            $this->adminOnly();
            $user = $this->em->find(User::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $b = $this->body();

            if (isset($b['nama'])) {
                $user->setNama($b['nama']);
            }
            if (isset($b['email'])) {
                $exists = $this->em->getRepository(User::class)->findOneBy(['email' => $b['email']]);
                if ($exists && $exists->getIdUser() !== $user->getIdUser()) {
                    $this->fail('Email sudah ada', 409);
                }
                $user->setEmail($b['email']);
            }
            if (isset($b['role'])) {
                $user->setRole($b['role']);
            }
            if (isset($b['password']) && $b['password'] !== '') {
                $user->setPassword(password_hash($b['password'], PASSWORD_BCRYPT));
            }

            $this->em->flush();
            $this->ok($user->toArray(), 'User diupdate');
        }

        public function delete(int $id): void {
            $this->adminOnly();
            $user = $this->em->find(User::class, $id) ?? $this->fail('Tidak ditemukan', 404);
            $this->em->remove($user);
            $this->em->flush();
            $this->ok(null, 'User dihapus');
        }
    }
?>