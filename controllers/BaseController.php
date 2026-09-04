<?php

require_once __DIR__ . '/../traits/JsonResponseTrait.php';

abstract class BaseController
{
    protected function respond(
        int $statusCode,
        array $payload
    ): void {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($payload);
    }
}