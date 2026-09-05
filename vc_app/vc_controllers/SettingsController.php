<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class SettingsController extends Controller
{
    public function general()
    {
        $app = Application::getInstance();
        $db = $app->getDb();
        $stmt = $db->query("SELECT * FROM vc_system_settings");
        $settings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_admin/vc_settings/general', ['settings' => $settings]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $app = Application::getInstance();
            $db = $app->getDb();

            foreach ($_POST as $key => $value) {
                $stmt = $db->prepare("UPDATE vc_system_settings SET setting_value = :value WHERE setting_key = :key");
                $stmt->execute(['value' => $value, 'key' => $key]);
            }

            $this->redirect('/admin/settings/general');
        }
    }
}