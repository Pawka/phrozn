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
 * @package     Phrozn\Server
 * @author      Osman Ungur
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Server;

/**
 * Class for parsing/reading HTTP request
 *
 * @package Phrozn\Server
 * @author Osman Ungur
 */
class Request {

    /**
     * Contains readed raw header
     *
     * @var string
     */
    private $rawHeader;

    /**
     * Contains parsed header values
     *
     * @var array
     */
    private $headers = array();

    /**
     * URI of parsed request
     *
     * @var string
     */
    private $requestUri;

    /**
     * Method of request
     *
     * @var string
     */
    private $requestMethod;

    /**
     * HTTP protocol version of request
     *
     * @var string
     */
    private $protocolVersion;

    function __construct($rawHeader) {
        $this->rawHeader = $rawHeader;
        $this->parse();
    }

    /**
     * Parses raw header
     */
    public function parse() {
        $headerParts = explode(PHP_EOL, $this->rawHeader);
        // First header parts includes status about request
        $statusLine = array_shift($headerParts);
        list($this->requestMethod, $this->requestUri, $this->protocolVersion) = explode(chr(32), $statusLine, 3);
        foreach ($headerParts as $part) {
            list($fieldName, $value) = explode(":", $part);
            $this->headers[$fieldName] = $value;
        }
    }

    /**
     * Returns request uri
     *
     * @return string
     */
    public function getRequestUri() {
        return $this->requestUri;
    }

    /**
     * Returns request method
     *
     * @return string
     */
    public function getRequestMethod() {
        return $this->requestMethod;
    }

    /**
     * Returns version of protocol
     *
     * @return string
     */
    public function getProtocolVersion() {
        return $this->protocolVersion;
    }

    /**
     * Returns value of request header field
     *
     * @param string $fieldName
     * @return string
     */
    public function getRequestField($fieldName) {
        if (array_key_exists($fieldName, $this->headers)) {
            return $this->headers[$fieldName];
        }
        return false;
    }

}
