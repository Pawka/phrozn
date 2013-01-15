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
 * @subpackage  Dao
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Registry\Dao;
use Phrozn\Registry\Dao;

/**
 * Serialized data store.
 *
 * @category    Phrozn
 * @package     Phrozn\Registry
 * @subpackage  Dao
 * @author      Victor Farazdagi
 */
class Serialized
    extends Base
    implements Dao
{
    /**
     * Save current registry container
     *
     * @return \Phorzn\Registry\Dao
     */
    public function save()
    {
        if ($path = $this->getProjectPath()) {
            $path .= '/' . $this->getOutputFile();
            file_put_contents($path, serialize($this->getContainer()));
        } else {
            throw new \RuntimeException('No project path provided.');
        }
        return $this;
    }

    /**
     * Read registry data into container.
     *
     * @return \Phrozn\Registry\Container
     */
    public function read()
    {
        $path = $this->getProjectPath() . '/' . $this->getOutputFile();
        if (!is_readable($path)) {
            $this->getContainer()->setValues(null);
            return $this->getContainer();
        }
        $newContainer = unserialize(file_get_contents($path));
        return $newContainer;
    }
}
