#!/usr/bin/php
<?php
namespace Phrozn;
use Phrozn\Runner\CommandLine as Runner,
    Zend\Loader\StandardAutoloader as Autoloader;

defined('PHROZN_PATH_APP') or define('PHROZN_PATH_APP', realpath(dirname(__FILE__) . '/../') . '/');
defined('PHROZN_PATH_BIN') or define('PHROZN_PATH_BIN', PHROZN_PATH_APP . 'bin/');
defined('PHROZN_PATH_LIB') or define('PHROZN_PATH_LIB', PHROZN_PATH_APP . 'library/');
defined('PHROZN_PATH_CONFIGS') or define('PHROZN_PATH_CONFIGS', PHROZN_PATH_APP . 'configs/');
defined('PHROZN_PATH_DOCS') or define('PHROZN_PATH_DOCS', PHROZN_PATH_APP . 'docs/');

// auto-loader
require_once PHROZN_PATH_LIB . 'Zend/Loader/StandardAutoloader.php';
$loader = new Autoloader();
$loader
    ->registerNamespace('Zend', PHROZN_PATH_LIB . 'Zend')
    ->registerNamespace('Phrozn', PHROZN_PATH_LIB . 'Phrozn')
    ->registerNamespace('Symfony', PHROZN_PATH_LIB . 'Symfony')
    ->setFallbackAutoloader(true)
    ->register();

Runner::run($loader);

