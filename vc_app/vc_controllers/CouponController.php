<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class CouponController extends Controller
{
    public function index()
    {
        $app = Application::getInstance();
        $db = $app->getDb();
        $stmt = $db->query("SELECT * FROM vc_coupons ORDER BY id DESC");
        $coupons = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('vc_admin/vc_coupons/index', ['coupons' => $coupons]);
    }
}