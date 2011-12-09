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
 * @package     Phrozn\Has
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Has;

/**
 * Entity has parameter
 *
 * @category    Phrozn
 * @package     Phrozn\Has
 * @author      Victor Farazdagi
 */
interface Param
{
    /**
     * Set param
     *
     * @param string $param Name of the parameter to set
     * @param mixed $value Value of the parameter
     *
     * @return \Phrozn\Has\Param
     */
    public function setParam($param, $value);

    /**
     * Get param value
     *
     * @param string $param Parameter name to obtain value for
     * @param mixed $default Default parameter value, if non found in FM
     *
     * @return mixed
     */
    public function getParam($param, $default = null);
}
