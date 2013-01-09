<?php
namespace Phrozn;
use Phrozn\Autoloader as Loader;

$path = realpath(dirname(__FILE__) . '/../');
set_include_path($path . PATH_SEPARATOR . get_include_path());

require_once $path . '/Phrozn/Autoloader.php';
$loader = Loader::getInstance();
$loader->getLoader(); // initialize autoloader

// Twig uses perverted file naming (due to absense of NSs at a time it was written)
// so fire up its own autoloader
require_once $path . '/Phrozn/Vendor/Twig/Autoloader.php';
\Twig_Autoloader::register();
