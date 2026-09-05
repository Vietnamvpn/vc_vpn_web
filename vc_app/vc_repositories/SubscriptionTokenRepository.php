<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class SubscriptionTokenRepository extends Repository
{
    protected $table = 'vc_subscription_tokens';

    public function findByToken($token)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}