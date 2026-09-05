<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class ReferralController extends Controller
{
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? 0;
        $app = Application::getInstance();
        $db = $app->getDb();

        $stmt = $db->prepare("SELECT * FROM vc_referrals WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $referrals = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_user/vc_referral/index', ['referrals' => $referrals]);
    }
}