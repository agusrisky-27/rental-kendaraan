<?php
    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\Tools\SchemaTool;
    use Doctrine\ORM\Tools\Setup;

    require_once __DIR__ . '/../vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    $dotenv->required(['JWT_SECRET', 'JWT_EXPIRE', 'DB_HOST', 'DB_NAME', 'DB_USER']);

    define('JWT_SECRET', $_ENV['JWT_SECRET']);
    define('JWT_EXPIRE', (int) $_ENV['JWT_EXPIRE']); // cast ke int karena dari .env selalu string

    function getEM(): EntityManager {
        $proxyDir = __DIR__ . '/../proxies';
        if (!is_dir($proxyDir)) {
            mkdir($proxyDir, 0777, true);
        }

        $config = Setup::createAttributeMetadataConfiguration(
            [__DIR__ . '/../src/Entity'],
            true,
            $proxyDir
        );

        $em = EntityManager::create(
            [
                'driver'   => 'pdo_mysql',
                'host'     => $_ENV['DB_HOST'],  // baca dari .env
                'dbname'   => $_ENV['DB_NAME'],  // baca dari .env
                'user'     => $_ENV['DB_USER'],  // baca dari .env
                'password' => $_ENV['DB_PASS'] ?? '', //dari inv
                'charset'  => 'utf8mb4',
            ],
            $config
        );

        $metadata = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            try {
                $schemaTool = new SchemaTool($em);
                $schemaTool->updateSchema($metadata, true);
            } catch (\Throwable $e) {
                $connection = $em->getConnection();
                if (!$connection->createSchemaManager()->tablesExist('pengembalian')) {
                    $connection->executeStatement(
                        "CREATE TABLE IF NOT EXISTS pengembalian (
                            id_pengembalian INT AUTO_INCREMENT NOT NULL,
                            id_transaksi    INT NOT NULL,
                            tanggal_kembali DATE NOT NULL,
                            kondisi_kendaraan VARCHAR(255) NOT NULL,
                            catatan         LONGTEXT DEFAULT NULL,
                            status          VARCHAR(255) NOT NULL,
                            PRIMARY KEY (id_pengembalian)
                        ) ENGINE = InnoDB"
                    );
                }
            }
        }

        return $em;
    }
?>