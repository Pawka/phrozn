<?php
namespace Phrozn;
use Zend\Loader\StandardAutoloader as Autoloader;

$path = realpath(dirname(__FILE__) . '/../') . '/library/';

// auto-loader
require_once $path . 'Zend/Loader/StandardAutoloader.php';
$loader = new Autoloader();
$loader
    ->registerNamespace('Zend', $path . 'Zend')
    ->registerNamespace('Phrozn', $path . 'Phrozn')
    ->registerNamespace('Symfony', $path . 'Symfony')
    ->registerNamespace('Twig', $path . 'Twig')
    ->setFallbackAutoloader(true)
    ->register();

// Twig uses perverted file naming (due to absense of NSs at a time it was written)
// so fire up its own autoloader
require_once $path . '/Twig/Autoloader.php';
\Twig_Autoloader::register();



