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
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Processor;
use Phrozn\Autoloader as Loader;

/**
 * SCSS styles processor
 *
 * @category    Phrozn
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 */
class Scss
    extends Base
    implements \Phrozn\Processor
{
    /**
     * Reference to SCSS compiler
     * @var \
     */
    protected $scssc;

    /**
     * If configuration options are passes then twig environment
     * is initialized right away
     *
     * @param array $options Processor options
     *
     * @return void
     */
    public function __construct($options = array())
    {
        $path = Loader::getInstance()->getPath('library');
        require_once $path . '/Vendor/Extra/scss.inc.php';

        if (count($options)) {
            $this->setConfig($options)
                 ->getEnvironment();
        }
    }

    /**
     * Parse the incoming template
     *
     * @param string $tpl Source template content
     * @param array $vars List of variables passed to template engine
     *
     * @return string Processed template
     */
    public function render($tpl, $vars = array())
    {
        return $this->getEnvironment()
                    ->compile($tpl);
    }

    protected function getEnvironment($reset = false)
    {
        if ($reset === true || null === $this->scssc) {
            $this->scssc = new \scssc;
        }

        return $this->scssc;
    }
}
