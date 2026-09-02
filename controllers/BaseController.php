<?php

require_once __DIR__ . '/../traits/JsonResponseTrait.php';

abstract class BaseController
{
    use JsonResponseTrait;

    protected function respond(int $statusCode, array $payload): void
    {
        $this->sendJson($statusCode, $payload);
    }
}
