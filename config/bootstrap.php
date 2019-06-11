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
$dbParams = [];

foreach (['driver', 'user', 'password', 'path', 'dbname'] as $param) {
	$value = getenv('DB_' . strtoupper($param));
	
	if ($value === false || strlen($value) === 0) {
		continue;
	}
	
	$dbParams[$param] = $value;
}

$config = Setup::createAnnotationMetadataConfiguration($entityPaths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);
