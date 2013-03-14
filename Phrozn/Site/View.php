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
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site;
use Phrozn\Has;

/**
 * Basic atom of site compilation process.
 * Site basically composed of number of views, which load
 * configuration and tempaltes and get compiled into HTML.
 *
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
interface View
    extends
        Has\SiteConfig,
        Has\InputRootDir,
        Has\InputFile,
        Has\OutputFile,
        Has\OutputDir,
        Has\Param,
        Has\Processors,
        Has\FrontMatter,
        Has\Template
{

    /**
     * Render and save static version of a concrete view
     *
     * @param array $vars List of variables passed to text processors
     *
     * @return \Phrozn\Site\View
     */
    public function compile($vars = array());

    /**
     * Render input template
     *
     * @param array $vars List of variables passed to text processors
     *
     * @return string
     */
    public function render($vars = array());
}
