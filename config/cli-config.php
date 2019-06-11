<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require __DIR__ . '/bootstrap.php';

return ConsoleRunner::createHelperSet($entityManager);
