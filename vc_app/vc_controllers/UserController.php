<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class UserController extends Controller
{
    public function index()
    {
        $app = Application::getInstance();
        $db = $app->getDb();
        $stmt = $db->query("SELECT * FROM vc_users ORDER BY id DESC");
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_admin/vc_users/index', ['users' => $users]);
    }

    public function profile()
    {
        $userId = $_SESSION['user_id'] ?? 0;
        $app = Application::getInstance();
        $db = $app->getDb();
        
        $stmt = $db->prepare("SELECT * FROM vc_users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->view('vc_user/profile', ['user' => $user]);
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? 0;
            $email = trim($_POST['email'] ?? '');

            $app = Application::getInstance();
            $db = $app->getDb();

            $stmt = $db->prepare("UPDATE vc_users SET email = :email WHERE id = :id");
            $stmt->execute(['email' => $email, 'id' => $userId]);

            $this->redirect('/user/profile');
        }
    }
}