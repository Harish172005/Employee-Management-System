<?php

trait JsonResponseTrait
{
    protected function sendJson(int $statusCode, array $payload): void
    {
        http_response_code($statusCode);
        echo json_encode($payload);
    }
}
