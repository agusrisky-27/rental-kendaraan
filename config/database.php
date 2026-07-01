<?php

use Doctrine\ORM\EntityManager;
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

    return EntityManager::create(
        [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'rental_kendaraan',
            'user' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
        $config
    );
}