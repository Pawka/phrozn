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
 * @package     Phrozn\Registry
 * @subpackage  Dao
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Registry\Dao;
use Phrozn\Registry\Dao,
    Phrozn\Path\Project as ProjectPath;

/**
 * Base implementaion of Registry DAO.
 *
 * @category    Phrozn
 * @package     Phrozn\Registry
 * @subpackage  Dao
 * @author      Victor Farazdagi
 */
abstract class Base
    implements Dao
{
    /**
     * Registry container
     * @var \Phrozn\Registry\Container
     */
    private $container;

    /**
     * @var string
     */
    private $projectPath;

    /**
     * Initialize DAO object
     *
     * @param \Phrozn\Registry\Container $container Registry container
     *
     * @return void
     */
    public function __construct(\Phrozn\Registry\Container $container = null)
    {
        $this->setContainer($container);
    }

    /**
     * Set registry container.
     *
     * @param \Phrozn\Registry\Container $container Registry container
     *
     * @return \Phrozn\Has\Container
     */
    public function setContainer(\Phrozn\Registry\Container $container = null)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Get registry container.
     *
     * @return \Phrozn\Has\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set project path.
     *
     * @param string $path Project path.
     *
     * @return \Phrozn\Has\ProjectPath
     */
    public function setProjectPath($path)
    {
        $path = new ProjectPath($path);
        $this->projectPath = $path->get(); // calculate path

        return $this;
    }

    /**
     * Get project path.
     *
     * @return string
     */
    public function getProjectPath()
    {
        return $this->projectPath;
    }

}
