<?php
require __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;

$dotenv = Dotenv::create(__DIR__ . '/..');
$dotenv->load();

$entityPaths = [
    __DIR__ . '/../src/Entities',
];
$isDevMode = true;
$dbParams = array(
    'driver'   => getenv('DB_DRIVER'),
    'user'     => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'path'   => getenv('DB_PATH'),
);

$config = Setup::createAnnotationMetadataConfiguration($entityPaths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);
