<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class PlanController extends Controller
{
    public function index()
    {
        $app = Application::getInstance();
        $db = $app->getDb();
        $stmt = $db->query("SELECT * FROM vc_vpn_plans WHERE status = 1");
        $plans = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_public/pricing', ['plans' => $plans]);
    }

    public function adminIndex()
    {
        $app = Application::getInstance();
        $db = $app->getDb();
        $stmt = $db->query("SELECT * FROM vc_vpn_plans ORDER BY id DESC");
        $plans = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_admin/vc_plans/index', ['plans' => $plans]);
    }
}