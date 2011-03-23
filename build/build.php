<?php
require_once('PEAR/PackageFileManager2.php');
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$pack = new PEAR_PackageFileManager2;
$outputDir = realpath(dirname(__FILE__) . '/../') . '/';

$e = $pack->setOptions(array(
    'baseinstalldir' => '/',
    'packagedirectory' => dirname(__FILE__) . '/../',
    'ignore' => array(
        'build/', 'tests/', 'extras/', 'plugin/',  
        'phrozn.png', '*.tgz',
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
        'bin/phrozn.php' => 'script',
        'bin/phr.php' => 'script',
        'LICENSE' => 'doc',
    ),
    'installexceptions' => array(
    )
));

$pack->setPackage('Phrozn');
$pack->setSummary('Static web-site generator in PHP.');
$pack->setDescription('Phrozn is static web-site generator written in PHP.');

$pack->setChannel('pear.phrozn.info');
$pack->setPackageType('php'); // this is a PEAR-style php script package

$pack->setReleaseVersion('0.1.0');
$pack->setAPIVersion('0.1.0');

$pack->setReleaseStability('beta');
$pack->setAPIStability('beta');

$pack->setNotes('
    * The first public release of Phrozn
');
$pack->setLicense('Apache License, Version 2.0', 'http://www.apache.org/licenses/LICENSE-2.0');

$pack->addMaintainer('lead', 'victor', 'Victor Farazdagi', 'simple.square@gmail.com');

$pack->addRelease();
$pack->addInstallAs('bin/phr.php', 'phr');
$pack->addInstallAs('bin/phrozn.php', 'phrozn');

$pack->setPhpDep('5.3.0');
$pack->setPearinstallerDep('1.4.6');

$pack->addReplacement('bin/phrozn.php', 'pear-config', '/usr/bin/env php', 'php_bin');
$pack->addReplacement('bin/phrozn.php', 'pear-config', '@PHP-BIN@', 'php_bin');
$pack->addReplacement('bin/phrozn.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pack->addReplacement('bin/phrozn.php', 'pear-config', '@PEAR-DIR@', 'php_dir');

$pack->addReplacement('bin/phr.php', 'pear-config', '/usr/bin/env php', 'php_bin');
$pack->addReplacement('bin/phr.php', 'pear-config', '@PHP-BIN@', 'php_bin');
$pack->addReplacement('bin/phr.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pack->addReplacement('bin/phr.php', 'pear-config', '@PEAR-DIR@', 'php_dir');

$pack->addReplacement('Phrozn/Autoloader.php', 'pear-config', '@PHP-BIN@', 'php_bin');
$pack->addReplacement('Phrozn/Autoloader.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pack->addReplacement('Phrozn/Autoloader.php', 'pear-config', '@PEAR-DIR@', 'php_dir');

$pack->generateContents();

echo $pack->writePackageFile();
echo 'Package file created: ' . $outputDir . 'package.xml';
