<?php
/**
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *          http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category    Phrozn
 * @package     Phrozn
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn;

/**
 * Phrozn autoloader
 *
 * @category    Phrozn
 * @package     Phrozn
 * @author      Victor Farazdagi
 */
class Autoloader
{
    /**
     * Singleton instance
     * @var \Phrozn\Autoloader
     * @see self::getInstance();
     */
    private static $instance;

    /**
     * @var \Composer\Autoload\ClassLoader
     */
    private $loader;

    /**
     * Singleton instance
     *
     * @return \Phrozn\Autoloader
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get auto-loader instance
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function getLoader()
    {
        if (null === $this->loader) {
            if (strpos('@PHP-BIN@', '@PHP-BIN') === 0) {
                $base = dirname(__FILE__) . '/';
                set_include_path($base . PATH_SEPARATOR . get_include_path());
            } else {
                $base = '@PEAR-DIR@/Phrozn/';
            }

            //Autoload candidates.
            $dirs = array($base . '..', getcwd());

            foreach ($dirs as $dir) {
                $file = $dir . '/vendor/autoload.php';
                if (file_exists($file)) {
                    $this->loader = include $file;
                    break;
                }
            }

            if (null === $this->loader) {
                throw new \RuntimeException("Unable to locate autoloader.");
            }
        }

        return $this->loader;
    }

    /**
     * Get base paths required to load extra resources
     *
     * @return array
     */
    public function getPaths()
    {
        if (strpos('@PHP-BIN@', '@PHP-BIN') === 0) {
            $dataDir = dirname(__FILE__) . '/../';
            $phpDir = dirname(__FILE__) . '/';
        } else {
            $dataDir = '@DATA-DIR@/Phrozn/';
            $phpDir = '@PEAR-DIR@/Phrozn/';
        }
        return array(
            'data_dir'      => $dataDir,
            'php_dir'       => $phpDir,
            'configs'   => $dataDir . 'configs/',
            'skeleton'  => $dataDir . 'skeleton/',
            'library'   => $phpDir,
        );
    }

    public function getPath($path)
    {
        $paths = $this->getPaths();
        return $paths[$path];
    }

    /**
     * Setup autoloader.
     *
     * @return void
     */
    private function __construct()
    {
        $loader = $this->getLoader();

        $this->loader = $loader;
    }

    private function __clone()
    {}
}
