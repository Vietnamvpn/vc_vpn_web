<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class PaymentController extends Controller
{
    public function index()
    {
        $app = Application::getInstance();
        $db = $app->getDb();
        $stmt = $db->query("SELECT * FROM vc_payments ORDER BY id DESC");
        $payments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_admin/vc_payments/index', ['payments' => $payments]);
    }

    public function webhook()
    {
        $payload = file_get_contents('php://input');
        // Xử lý xác thực webhook thanh toán tại đây
        http_response_code(200);
        echo json_encode(['status' => 'success']);
        exit;
    }
}