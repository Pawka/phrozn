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
 * @subpackage  Has
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Registry\Has;

/**
 * Has DAO attached.
 *
 * @category    Phrozn
 * @package     Phrozn\Registry
 * @subpackage  Has
 * @author      Victor Farazdagi
 */
interface Dao
{
    /**
     * Set DAO.
     *
     * @param \Phrozn\Registry\Dao $dao Data access object
     *
     * @return \Phrozn\Has\Dao
     */
    public function setDao(\Phrozn\Registry\Dao $dao);

    /**
     * Get DAO.
     *
     * @return \Phrozn\Has\Dao
     */
    public function getDao();
}
