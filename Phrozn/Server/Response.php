<?php

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

    private $headers = array();
    private $content = false;
    private $responseCode = 200;
    private $charset = 'UTF-8';
    private $protocolVersion = 1.1;
    private $mimeType = 'text/html';
    // Support for common status codes, will add other codes for future if needed
    private $responseCodes = array(
        200 => 'OK',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error'
    );

    function __construct() {
        $this->setResponseField('Server', self::SERVERNAME);
    }

    public function setResponseField($key, $value) {
        $this->headers[$key] = $value;
    }

    public function getResponseField($key) {
        return $this->headers[$key];
    }

    public function setResponseCode($responseCode) {
        if (array_key_exists($responseCode, $this->responseCodes)) {
            $this->responseCode = $responseCode;
        }
        return false;
    }

    public function getResponseCode() {
        return $this->responseCode;
    }

    public function setProtocolVersion($protocolVersion) {
        $this->protocolVersion = $protocolVersion;
    }

    public function setCharset($charset) {
        $this->charset = $charset;
    }

    public function setMimeType($mimeType) {
        $this->mimeType = $mimeType;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getRawResponse() {
        return implode(self::DELIMITER . self::DELIMITER, array($this->getHeaders(), $this->getContent()));
    }

    public function getHeaders() {
        $pieces = array_merge(
                array(
            sprintf("HTTP/%s %s %s", $this->protocolVersion, $this->responseCode, $this->responseCodes[$this->responseCode]),
            sprintf("Content-Type: %s; charset=%s", $this->mimeType, $this->charset)
                ), $this->headers
        );
        return implode(self::DELIMITER, $pieces);
    }

    public function getContent() {
        return $this->content;
    }

}