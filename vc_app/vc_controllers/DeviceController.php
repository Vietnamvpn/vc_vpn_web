<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class DeviceController extends Controller
{
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? 0;
        $app = Application::getInstance();
        $db = $app->getDb();

        $stmt = $db->prepare("SELECT * FROM vc_user_devices WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $devices = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_user/devices', ['devices' => $devices]);
    }
}