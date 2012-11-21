<?php
include __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application,
    PhpAutoTest\Console\Autotest;

// echo PHP_OS;
$application = new Application();
$application->add(new Autotest);
$application->run();