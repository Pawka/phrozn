<?php
use Zend\Loader\StandardAutoloader as Autoloader;

// setup auto-loader
// phrozn uses ZF2 autoloader
$path = dirname(__FILE__) . '/Vendor/';

require_once $path . 'Zend/Loader/StandardAutoloader.php';
$loader = new Autoloader();
$loader
    ->registerNamespace('Zend', $path . 'Zend')
    ->registerNamespace('Phrozn', $path . 'Phrozn')
    ->registerNamespace('Symfony', $path . 'Symfony')
    ->registerNamespace('Twig', $path . 'Twig')
    ->setFallbackAutoloader(true)
    ->register();
