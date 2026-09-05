<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class SupportController extends Controller
{
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? 0;
        $app = Application::getInstance();
        $db = $app->getDb();

        $stmt = $db->prepare("SELECT * FROM vc_support_tickets WHERE user_id = :user_id ORDER BY id DESC");
        $stmt->execute(['user_id' => $userId]);
        $tickets = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_user/vc_support/index', ['tickets' => $tickets]);
    }
}