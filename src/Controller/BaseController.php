<?php
namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class BaseController
{
    protected EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function body(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // Kalau form-data atau x-www-form-urlencoded
        if (str_contains($contentType, 'multipart/form-data') || 
            str_contains($contentType, 'application/x-www-form-urlencoded')) {
            return $_POST;
        }

        // Default raw JSON
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    protected function ok(mixed $data, string $msg = 'Berhasil', int $code = 200): never
    {
        http_response_code($code);
        echo json_encode(['success' => true, 'message' => $msg, 'data' => $data]);
        exit;
    }

    protected function fail(string $msg, int $code = 400): never
    {
        http_response_code($code);
        echo json_encode(['success' => false, 'message' => $msg]);
        exit;
    }

    protected function auth(): array
    {
        $header = getallheaders()['Authorization'] ?? '';
        if (!str_starts_with($header, 'Bearer ')) $this->fail('Token tidak ada', 401);
        try {
            return (array) JWT::decode(substr($header, 7), new Key(JWT_SECRET, 'HS256'));
        } catch (\Exception) {
            $this->fail('Token tidak valid', 401);
        }
    }

    protected function adminOnly(): array
    {
        $p = $this->auth();
        if ($p['role'] !== 'admin') $this->fail('Hanya admin', 403);
        return $p;
    }
}
