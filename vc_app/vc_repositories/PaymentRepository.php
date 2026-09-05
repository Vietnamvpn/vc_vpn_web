<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class PaymentRepository extends Repository
{
    protected $table = 'vc_payments';

    public function findByTransactionId($txnId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE transaction_id = :txn_id LIMIT 1");
        $stmt->execute(['txn_id' => $txnId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}