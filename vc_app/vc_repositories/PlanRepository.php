<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class PlanRepository extends Repository
{
    protected $table = 'vc_vpn_plans';

    public function getActivePlans()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE status = 1 ORDER BY id DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}