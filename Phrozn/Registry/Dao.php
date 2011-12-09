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
 * @package     Phrozn\Registry
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Registry;
use Phrozn,
    Phrozn\Registry\Has;

/**
 * Phrozn registry data access layer
 *
 * It is planned to rely on YAML files for storage, but that can be easily amended
 * by implementing more DAOs
 *
 * @category    Phrozn
 * @package     Phrozn\Registry
 * @author      Victor Farazdagi
 */
interface Dao
    extends Has\Container,
            Phrozn\Has\OutputFile,
            Phrozn\Has\ProjectPath
{
    /**
     * Initialize DAO object
     *
     * @param \Phrozn\Registry\Container $container Registry container
     *
     * @return void
     */
    public function __construct(\Phrozn\Registry\Container $container = null);

    /**
     * Save current registry container
     *
     * @return \Phorzn\Registry\Dao
     */
    public function save();

    /**
     * Read registry data into container.
     *
     * @return \Phrozn\Registry\Container
     */
    public function read();
}
