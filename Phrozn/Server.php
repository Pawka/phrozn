<?php

namespace Phrozn;

/**
 * Class for managing connections
 *
 * @package Phrozn
 * @author Osman Ungur
 */
class Server {

    /**
     *
     * @var string
     */
    private $socketAddress = 'tcp://0.0.0.0:8000';

    /**
     *
     * @var resource
     */
    private $socket;

    /**
     *
     * @var resource
     */
    private $connection;

    /**
     * Sets address of socket
     * 
     * @param string $socketAddress 
     */
    public function setSocketAddress($socketAddress) {
        $this->socketAddress = $socketAddress;
    }

    /**
     * Creates stream socket for accepting connections
     * 
     * @return bool
     * @throws ServerConnectionException
     */
    public function createSocket() {
        $this->socket = stream_socket_server($this->socketAddress, $errno, $errstr);
        if (!$this->socket) {
            throw new ServerConnectionException(sprintf("Socket cannot be created, Error : %s, %s", $errstr, $errno), 1);
        }
        $this->out("Starting Phrozn Server at " . $this->socketAddress);
        return true;
    }

    /**
     * Accepts and response connections
     */
    public function acceptConnection() {
        $this->out("Waiting for connections..");
        while ($this->connection = stream_socket_accept($this->socket, 1800)) {
            $this->sendResponse();
            $this->closeConnection();
        }
        $this->closeSocket();
    }

    /**
     * Closes stream socket
     * 
     * @return bool
     */
    private function closeSocket() {
        return fclose($this->socket);
    }

    /**
     * Closes a socket created connection
     * 
     * @return bool
     */
    public function closeConnection() {
        return fclose($this->connection);
    }

    /**
     * Returns current request object
     * 
     * @return Server\Request 
     */
    public function getRequest() {
        $rawRequest = trim(fread($this->connection, 8192));
        return new Server\Request($rawRequest);
    }

    /**
     * Returns content of resource
     * 
     * @param string $resource
     * @return string 
     */
    public function getContents($resource) {
        return file_get_contents($resource);
    }

    /**
     * Creates and returns response object
     * 
     * @return Server\Response 
     */
    public function getResponse() {
        $response = new Server\Response();
        $resource = $this->getResource();
        $fileinfo = new \SplFileInfo($resource);

        if (!$fileinfo->isReadable()) {
            $response->setResponseCode(404);
            $response->setContent('<h3>File not found<h3/>');
            return $response;
        }

        switch ($fileinfo->getType()) {
            case 'file':
                $response->setResponseCode(200);
                $response->setContent($this->getContents($resource));
                $response->setMimeType($this->getMimeType($resource));
                break;

            case 'dir':
                $indexfile = $resource . DIRECTORY_SEPARATOR . 'index.html';
                if (is_file($indexfile)) {
                    $response->setResponseCode(200);
                    $response->setContent($this->getContents($indexfile));
                } else {
                    $response->setResponseCode(200);
                    $response->setContent($this->getIndexOfFolder($resource));
                }
                break;

            default:
                $response->setResponseCode(500);
                $response->setContent('<h3>Internal server error<h3/>');
                break;
        }

        return $response;
    }

    /**
     * Writes contents of response to connection stream
     * 
     * @return int
     */
    public function sendResponse() {
        return fwrite($this->connection, $this->getResponse()->getRawResponse());
    }

    /**
     * Returns resource path
     * 
     * @return string
     */
    public function getResource() {
        return getcwd() . $this->getRequest()->getRequestUri();
    }

    /**
     * Writes messages to console
     * 
     * @param string $value 
     */
    public function out($value) {
        fwrite(STDOUT, $value . "\n");
    }

    /**
     * Returns mime type by resource path
     * 
     * @param string $resource
     * @return string
     */
    function getMimeType($resource) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $resource);
        finfo_close($finfo);
        return $type;
    }

    /**
     * Returns HTML representation of folders/files in path
     * 
     * @param string $path
     * @return string 
     */
    public function getIndexOfFolder($path) {
        $directory = new \DirectoryIterator($path);
        $result = new \ArrayObject();
        foreach ($directory as $fileinfo) {
            if ($fileinfo->isDir()) {
                $result->append(sprintf('<li><strong><a href="%s/">%s</a></strong></li>', $fileinfo->getBasename(), $fileinfo->getFilename()));
            } else {
                $result->append(sprintf('<li><a href="%s">%s</a></li>', $fileinfo->getBasename(), $fileinfo->getFilename()));
            }
        }
        return implode(PHP_EOL, iterator_to_array($result));
    }

}