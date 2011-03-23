#!/usr/bin/env php
<?php
namespace Phrozn;
use Phrozn\Runner\CommandLine as Runner,
    Phrozn\Autoloader as Loader;

if (strpos('@PHP-BIN@', '@PHP-BIN') === 0) { // stand-alone version is running
    $base = dirname(__FILE__) . '/../';
    set_include_path($base . PATH_SEPARATOR . get_include_path());
}

require_once 'Phrozn/Autoloader.php';
$loader = Loader::getInstance();
$runner = new Runner($loader);
$runner->run();

unset($runner, $loader);

