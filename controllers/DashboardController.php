<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../services/DashboardService.php';

class DashboardController extends BaseController
{
    public function getDashboard(): void
    {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        $result = (new DashboardService())->getDashboard();

        $this->respond($result['statusCode'] ?? 500, [
            'success' => $result['success'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null
        ]);
    }
}
