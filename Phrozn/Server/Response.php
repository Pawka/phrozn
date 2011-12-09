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
 * Class for creating HTTP responses
 *
 * @package Phrozn\Server
 * @author Osman Ungur
 */
class Response {
    const DELIMITER = "\r\n";
    const SERVERNAME = "Phrozn";

    /**
     * Response headers
     *
     * @var array
     */
    private $headers = array();

    /**
     * Response contents
     *
     * @var string
     */
    private $content = false;

    /**
     * Response status code
     *
     * @var int
     */
    private $responseCode = 200;

    /**
     * Response character encoding
     *
     * @var string
     */
    private $charset = 'UTF-8';

    /**
     * Response HTTP protocol version
     *
     * @var float|int
     */
    private $protocolVersion = 1.1;

    /**
     * Media type of content
     *
     * @var string
     */
    private $mimeType = 'text/html';

    /**
     * Common response codes
     *
     * @var array
     */
    private $responseCodes = array(
        200 => 'OK',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error'
    );

    function __construct() {
        $this->setResponseField('Server', self::SERVERNAME);
    }
    /**
     * Sets a header field
     *
     * @param string $key
     * @param string $value
     */
    public function setResponseField($key, $value) {
        $this->headers[$key] = $value;
    }

    /**
     * Returns value of header field
     *
     * @param string $key
     * @return string
     */
    public function getResponseField($key) {
        return $this->headers[$key];
    }

    /**
     * Sets response code of header
     *
     * @param int $responseCode
     * @return bool
     */
    public function setResponseCode($responseCode) {
        if (array_key_exists($responseCode, $this->responseCodes)) {
            $this->responseCode = $responseCode;
            return true;
        }
        return false;
    }

    /**
     * Returns response code of header
     *
     * @return int
     */
    public function getResponseCode() {
        return $this->responseCode;
    }

    /**
     * Sets HTTP protocol version
     *
     * @param float|int $protocolVersion
     */
    public function setProtocolVersion($protocolVersion) {
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * Sets character encoding
     *
     * @param string $charset
     */
    public function setCharset($charset) {
        $this->charset = $charset;
    }

    /**
     * Sets media type of content
     *
     * @param string $mimeType
     */
    public function setMimeType($mimeType) {
        $this->mimeType = $mimeType;
    }

    /**
     * Sets content
     *
     * @param string $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * Returns raw response for sending as response
     *
     * @return string
     */
    public function getRawResponse() {
        return implode(self::DELIMITER . self::DELIMITER, array($this->getHeaders(), $this->getContent()));
    }

    /**
     * Returns headers of response
     *
     * @return string
     */
    public function getHeaders() {
        $pieces = array_merge(
                array(
            sprintf("HTTP/%s %s %s", $this->protocolVersion, $this->responseCode, $this->responseCodes[$this->responseCode]),
            sprintf("Content-Type: %s; charset=%s", $this->mimeType, $this->charset)
                ), $this->headers
        );
        return implode(self::DELIMITER, $pieces);
    }

    /**
     * Returns content
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

}
