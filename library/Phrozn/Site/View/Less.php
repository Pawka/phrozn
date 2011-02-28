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
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site\View;
use Phrozn\Site,
    Phrozn\Processor\Less as Processor;

/**
 * LESS View
 *
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class Less 
    extends Base
    implements Site\View  
{
    /**
     * Initialize view
     *
     * @param string $inputFile Path to view source file
     * @param string $outputDir File destination path
     *
     * @return \Phrozn\Site\View
     */
    public function __construct($inputFile = null, $outputDir = null)
    {
        parent::__construct($inputFile, $outputDir);

        $processor = new Processor();
        $this->addProcessor(new Processor());
    }

    /**
     * Get output file path
     *
     * @return string
     */
    public function getOutputFile()
    {
        $path = parent::getOutputFile();
        if (null === $path) { // override only if not explicitly set
            // find relative path, wrt to styles
            $pos = strpos($this->getInputFile(), '/styles');
            if ($pos !== false) {
                $path = $this->getOutputDir() 
                      . substr($this->getInputFile(), $pos);
            } else {
                $path = $this->getOutputDir() 
                      . '/styles/'
                      . basename($this->getInputFile());
            }
            $path = dirname($path) . '/' . basename($path, '.less') . '.css';
            $this->setOutputFile($path);
        }
        return $path;
    }

}
