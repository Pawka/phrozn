<?php
namespace Phrozn\Server;

/**
 * Class for parsing/reading HTTP request
 *
 * @package Phrozn\Server
 * @author Osman Ungur
 */
class Request {

    private $rawHeader;
    private $headers = array();
    private $requestUri;
    private $requestMethod;
    private $protocolVersion;

    function __construct($rawHeader) {
        $this->rawHeader = $rawHeader;
        $this->parse();
    }

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

    public function getRequestUri() {
        return $this->requestUri;
    }

    public function getRequestMethod() {
        return $this->requestMethod;
    }

    public function getProtocolVersion() {
        return $this->protocolVersion;
    }

    public function getRequestField($fieldName) {
        if (array_key_exists($fieldName, $this->headers)) {
            return $this->headers[$fieldName];
        }
        return false;
    }

}