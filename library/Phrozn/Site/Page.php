<?php
/**
 * Copyright 2011 Victor Farazdagi
 *
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
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site;
use Phrozn\Has;

/**
 * Phrozn Page Abstraction
 *
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
interface Page 
    extends 
        Has\Source, 
        Has\Destination, 
        Has\Processor
{
    /**
     * Render and save static version of a concrete page
     *
     * @param array $vars List of variables passed to template engine
     *
     * @return \Phrozn\Site\Page
     */
    public function compile($vars);

    /**
     * Render input template
     *
     * @param array $vars List of variables passed to template engine
     *
     * @return string
     */
    public function render($vars);

    /**
     * Get page name
     *
     * @return string
     */
    public function getName();
}
