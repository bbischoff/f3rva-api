<?php
namespace F3\Util;

/**
 * Interface for all classes making HTTP requests
 *
 * @author bbischoff
 */
interface HttpRequest
{
    public function close();
    public function execute();
    public function getInfo($name);
    public function init($url);
    public function setOption($name, $value);
}