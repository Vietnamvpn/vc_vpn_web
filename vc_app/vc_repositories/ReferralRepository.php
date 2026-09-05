<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class ReferralRepository extends Repository
{
    protected $table = 'vc_referrals';

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}