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
 * @package     Phrozn\Site\Layout
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site\Layout;
use Phrozn\Site;

/**
 * Phrozn default layout implementation
 *
 * @category    Phrozn
 * @package     Phrozn\Site\Layout
 * @author      Victor Farazdagi
 */
class DefaultLayout
    implements Site\Layout
{
    /**
     * Input file
     * @var string
     */
    private $sourcePath;

    /**
     * Markup processor
     * @var \Phrozn\Processor
     */
    private $processor;

    /**
     * Template source text
     * @var string
     */
    private $source;

    /**
     * Initialize layout
     *
     * @param string $sourcePath Path to layout file
     * @param \Phrozn\Process $processor Markup processor
     *
     * @return \Phrozn\Site\Layout
     */
    public function __construct($sourcePath = null, $processor = null)
    {
        $this
            ->setSourcePath($sourcePath)
            ->setProcessor($processor);
    }

    /**
     * Render layout template
     *
     * @param array $vars List of variables passed to template engine
     *
     * @return string
     */
    public function render($vars)
    {
        $layoutPath = $this->getSourcePath();
        
        return $this->getProcessor()
                       ->render($this->readSourceFile(), $vars);
    }

    /**
     * Set layout file path
     *
     * @param string $path Path to source file
     *
     * @return \Phrozn\Site\Layout
     */
    public function setSourcePath($path)
    {
        $this->sourcePath = $path;
        return $this;
    }

    /**
     * Get layout input file path
     *
     * @return string
     */
    public function getSourcePath()
    {
        return $this->sourcePath;
    }

    /**
     * Set markup processor
     *
     * @param \Phrozn\Processor
     *
     * @return \Phrozn\Site\Layout
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
    }

    /**
     * Get markup processor
     *
     * @return \Phrozn\Processor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Read input file
     *
     * @return string
     */
    private function readSourceFile()
    {
        if (null == $this->source) {
            $path = $this->getSourcePath();
            if (null === $path) {
                throw new \Exception("Layout's source file not specified.");
            }

            try {
                $this->source = \file_get_contents($path);
            } catch (\Exception $e) {
                $filename = basename($path);
                throw new \Exception("Layout file '{$filename}' cannot be read.");
            }
        }
        return $this->source;
    }
}
