<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class NodeRepository extends Repository
{
    protected $table = 'vc_vpn_nodes';

    public function getActiveNodes()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE status = 1");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}