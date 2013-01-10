#!/usr/bin/env php
<?php
use Phrozn\Autoloader as Loader;
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// rely on configuration files
require_once dirname(__FILE__) . '/../Phrozn/Autoloader.php';
$loader = Loader::getInstance()->getLoader();
$config = new \Phrozn\Config(dirname(__FILE__) . '/../configs/');

require_once('PEAR/PackageFileManager2.php');
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$pack = new PEAR_PackageFileManager2;
$outputDir = realpath(dirname(__FILE__) . '/../') . '/';
$inputDir = realpath(dirname(__FILE__) . '/../');

$e = $pack->setOptions(array(
    'baseinstalldir' => '/',
    'packagedirectory' => $inputDir,
    'ignore' => array(
        'build/', 'tests/', 'extras/', 'plugin/',
        'phrozn.png', '*.tgz', 'bin/release', 'tags',
    ),
    'outputdirectory' => $outputDir,
    'simpleoutput' => true,
    'roles' => array(
        'textile' => 'doc'
    ),
    'dir_roles' => array(
        'Phrozn'    => 'php',
        'configs'   => 'data',
        'skeleton'  => 'data',
        'tests'     => 'test',
    ),
    'exceptions' => array(
        'bin/phr.bat' => 'script',
        'bin/phrozn.php' => 'script',
        'bin/phr.php' => 'script',
        'LICENSE' => 'doc',
    ),
    'installexceptions' => array(
    ),
    'clearchangelog' => true,
));

$pack->setPackage('Phrozn');
$pack->setSummary($config['phrozn']['summary']);
$pack->setDescription($config['phrozn']['description']);

$pack->setChannel('pear.phrozn.info');
$pack->setPackageType('php'); // this is a PEAR-style php script package

$pack->setReleaseVersion($config['phrozn']['version']);
$pack->setAPIVersion($config['phrozn']['version']);

$pack->setReleaseStability($config['phrozn']['stability']);
$pack->setAPIStability($config['phrozn']['stability']);

$pack->setNotes('
    * The first public release of Phrozn
');
$pack->setLicense('Apache License, Version 2.0', 'http://www.apache.org/licenses/LICENSE-2.0');

$pack->addMaintainer('lead', 'victor', 'Victor Farazdagi', 'simple.square@gmail.com');

$pack->addRelease();
$pack->setOSInstallCondition('windows');
$pack->addInstallAs('bin/phr.bat', 'phrozn.bat');
$pack->addInstallAs('bin/phrozn.php', 'phrozn');

$pack->addRelease();
$pack->addIgnoreToRelease('bin/phr.bat');
$pack->addInstallAs('bin/phr.php', 'phr');
$pack->addInstallAs('bin/phrozn.php', 'phrozn');

// core dependencies
$pack->setPhpDep('5.3.0');
$pack->setPearinstallerDep('1.4.6');

// package dependencies
$pack->addPackageDepWithChannel('required', 'Console_CommandLine', 'pear.php.net', '1.1.3');
$pack->addPackageDepWithChannel('required', 'Console_Color', 'pear.php.net', '1.0.3');
$pack->addPackageDepWithChannel('required', 'Console_Table', 'pear.php.net', '1.1.4');
$pack->addPackageDepWithChannel('required', 'Archive_Tar', 'pear.php.net', '1.3.7');

$pack->addReplacement('bin/phrozn.php', 'pear-config', '/usr/bin/env php', 'php_bin');
$pack->addReplacement('bin/phrozn.php', 'pear-config', '@PHP-BIN@', 'php_bin');
$pack->addReplacement('bin/phrozn.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pack->addReplacement('bin/phrozn.php', 'pear-config', '@PEAR-DIR@', 'php_dir');

$pack->addReplacement('bin/phr.bat', 'pear-config', '@php_bin@', 'php_bin');
$pack->addReplacement('bin/phr.bat', 'pear-config', '@bin_dir@', 'bin_dir');

$pack->addReplacement('bin/phr.php', 'pear-config', '/usr/bin/env php', 'php_bin');
$pack->addReplacement('bin/phr.php', 'pear-config', '@PHP-BIN@', 'php_bin');
$pack->addReplacement('bin/phr.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pack->addReplacement('bin/phr.php', 'pear-config', '@PEAR-DIR@', 'php_dir');

$pack->addReplacement('Phrozn/Autoloader.php', 'pear-config', '@PHP-BIN@', 'php_bin');
$pack->addReplacement('Phrozn/Autoloader.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pack->addReplacement('Phrozn/Autoloader.php', 'pear-config', '@PEAR-DIR@', 'php_dir');

$pack->generateContents();

$pack->writePackageFile();
echo 'Package file created: ' . $outputDir . 'package.xml' . "\n";
