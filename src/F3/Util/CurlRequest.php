<?php
namespace F3\Util;

/**
 * Utility class for making Curl HTTP requests
 *
 * @author bbischoff
 */
class CurlRequest implements HttpRequest
{
    private $handle = null;

    public function close() {
        curl_close($this->handle);
    }

    public function execute() {
        return curl_exec($this->handle);
    }

    public function getInfo($name) {
        return curl_getinfo($this->handle, $name);
    }

    public function init($url) {
        $this->handle = curl_init($url);
    }

    public function setOption($name, $value) {
        curl_setopt($this->handle, $name, $value);
    }
}