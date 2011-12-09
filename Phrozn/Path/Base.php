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
 * @package     Phrozn\Path
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Path;
use Phrozn\Path;

/**
 * Base implementation.
 *
 * @category    Phrozn
 * @package     Phrozn\Path
 * @author      Victor Farazdagi
 */
abstract class Base
    implements Path
{
    /**
     * Source path
     * @var
     */
    protected $path;

    /**
     * Initialize path builder
     *
     * @param string $path Starting path to deduce where .phrozn is located
     *
     * @return void
     */
    public function __construct($path = null)
    {
        $this->set($path);
    }

    /**
     * Set source path
     *
     * @param string $path Source path
     *
     * @return \Phrozn\Path
     */
    public function set($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Convert object to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }
}
