#!/usr/bin/env php
<?php

$base = dirname(__FILE__) . '/../';
if (strpos('@PHP-BIN@', '@PHP-BIN') === 0) { // stand-alone version is running
    set_include_path($base . PATH_SEPARATOR . get_include_path());
}

require_once $base . '/app/bootstrap.php';

$loader = Phrozn\Autoloader::getInstance();
$runner = new Phrozn\Runner\CommandLine($loader);
$runner->run();

unset($runner, $loader);
