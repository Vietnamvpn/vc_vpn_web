<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class NodeController extends Controller
{
    public function index()
    {
        $app = Application::getInstance();
        $db = $app->getDb();
        $stmt = $db->query("SELECT * FROM vc_vpn_nodes ORDER BY id DESC");
        $nodes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_admin/vc_nodes/index', ['nodes' => $nodes]);
    }
}