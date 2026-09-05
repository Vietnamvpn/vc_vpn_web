<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class CouponRepository extends Repository
{
    protected $table = 'vc_coupons';

    public function findByCode($code)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE code = :code AND status = 1 LIMIT 1");
        $stmt->execute(['code' => $code]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}