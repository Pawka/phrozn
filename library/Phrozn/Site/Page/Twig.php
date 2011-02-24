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
 * @package     Phrozn\Site\Page
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site\Page;
use Phrozn\Site,
    Phrozn\Processor\Twig as Processor;

/**
 * Phrozn Page in Twig format
 *
 * @category    Phrozn
 * @package     Phrozn\Site\Page
 * @author      Victor Farazdagi
 */
class Twig 
    extends BasePage
    implements Site\Page
{
    /**
     * Initialize page
     *
     * @param string $source Page source file
     * @param string $destination Page destination path
     * @param \Phrozn\Process $processor Phrozn markup processor
     *
     * @return \Phrozn\Site\Page
     */
    public function __construct($source = null, $destination = null)
    {
        parent::__construct($source, $destination);

        $processor = new Processor();
        $this->setProcessor(new Processor());
    }
}
