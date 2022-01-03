<?php

namespace F3\Resource;

/*
 * Abstract REST controller for all resources
 */
abstract class AbstractResource {

    public const HEADER_KEY = 'status_code_header';
    public const BODY_KEY = 'body';

    protected function createResponse($statusCode, $body) {
        $response[self::HEADER_KEY] = HttpStatusCode::httpHeaderFor($statusCode);
        $response[self::BODY_KEY] = $body;

        return $response;
    }
}

?>
