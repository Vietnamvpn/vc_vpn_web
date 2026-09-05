<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class InvoiceRepository extends Repository
{
    protected $table = 'vc_invoices';

    public function getByOrderId($orderId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE order_id = :order_id LIMIT 1");
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}