<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class SubscriptionAccessRepository extends Repository
{
    protected $table = 'vc_subscription_access';

    public function getBySubscriptionId($subscriptionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE subscription_id = :sub_id");
        $stmt->execute(['sub_id' => $subscriptionId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}