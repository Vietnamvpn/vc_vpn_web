<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class OrderRepository extends Repository
{
    protected $table = 'vc_orders';

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY id DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}