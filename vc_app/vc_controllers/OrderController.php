<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class OrderController extends Controller
{
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? 0;
        $app = Application::getInstance();
        $db = $app->getDb();
        
        $stmt = $db->prepare("SELECT * FROM vc_orders WHERE user_id = :user_id ORDER BY id DESC");
        $stmt->execute(['user_id' => $userId]);
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_user/vc_orders/index', ['orders' => $orders]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? 0;
            $planId = $_POST['plan_id'] ?? 0;

            $app = Application::getInstance();
            $db = $app->getDb();

            $stmt = $db->prepare("INSERT INTO vc_orders (user_id, plan_id, status, created_at) VALUES (:user_id, :plan_id, 'pending', NOW())");
            $stmt->execute(['user_id' => $userId, 'plan_id' => $planId]);
            $orderId = $db->lastInsertId();

            $this->redirect("/user/orders/detail?id={$orderId}");
        }
    }
}