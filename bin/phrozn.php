#!/usr/bin/php
<?php
namespace Phrozn;
use Phrozn\Runner\CommandLine as Runner,
    Zend\Loader\StandardAutoloader as Autoloader;


// paths
$base = realpath(dirname(__FILE__) . '/../') . '/';
$paths = array(
    'app'       => $base,
    'bin'       => $base . 'bin/',
    'lib'       => $base . 'library/',
    'configs'   => $base . 'configs/',
);

// auto-loader
require_once $paths['lib'] . 'Zend/Loader/StandardAutoloader.php';
$loader = new Autoloader();
$loader
    ->registerNamespace('Zend', $paths['lib'] . 'Zend')
    ->registerNamespace('Phrozn', $paths['lib'] . 'Phrozn')
    ->registerNamespace('Symfony', $paths['lib'] . 'Symfony')
    ->setFallbackAutoloader(true)
    ->register();

$runner = new Runner($loader, $paths);
$runner->run();
unset($runner, $loader, $paths, $base);

