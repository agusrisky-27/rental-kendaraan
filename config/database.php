<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;

require_once __DIR__ . '/../vendor/autoload.php';

define('JWT_SECRET', 'rental_kendaraan_secret_key_itb_stikom_bali_2024');
define('JWT_EXPIRE', 86400);

function getEM(): EntityManager
{
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
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'dbname' => 'rental_kendaraan',
            'user' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
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
                        id_transaksi INT NOT NULL,
                        tanggal_kembali DATE NOT NULL,
                        kondisi_kendaraan VARCHAR(255) NOT NULL,
                        catatan LONGTEXT DEFAULT NULL,
                        status VARCHAR(255) NOT NULL,
                        PRIMARY KEY (id_pengembalian),
                        INDEX IDX_pengembalian_transaksi (id_transaksi)
                    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
                );
            }
        }
    }

    return $em;
}
