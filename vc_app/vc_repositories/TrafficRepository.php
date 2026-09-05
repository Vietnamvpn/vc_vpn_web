<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class TrafficRepository extends Repository
{
    protected $table = 'vc_subscription_traffic';

    public function getBySubscriptionId($subscriptionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE subscription_id = :sub_id ORDER BY id DESC");
        $stmt->execute(['sub_id' => $subscriptionId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}