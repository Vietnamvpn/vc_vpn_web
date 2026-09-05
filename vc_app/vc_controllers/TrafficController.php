<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class TrafficController extends Controller
{
    public function index()
    {
        $app = Application::getInstance();
        $db = $app->getDb();
        $stmt = $db->query("SELECT * FROM vc_subscription_traffic ORDER BY id DESC LIMIT 100");
        $trafficLogs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_admin/vc_traffic/index', ['trafficLogs' => $trafficLogs]);
    }
}