<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class InvoiceController extends Controller
{
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $app = Application::getInstance();
        $db = $app->getDb();

        $stmt = $db->prepare("SELECT * FROM vc_invoices WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->view('vc_admin/vc_invoices/detail', ['invoice' => $invoice]);
    }
}