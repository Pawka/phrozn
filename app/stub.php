#!/usr/bin/env php
<?php

Phar::mapPhar('phrozn.phar');
require_once 'phar://phrozn.phar/Phrozn/Autoloader.php';

$loader = Phrozn\Autoloader::getInstance();
$runner = new Phrozn\Runner\CommandLine($loader);
$runner->run();

unset($runner, $loader);
__HALT_COMPILER();

