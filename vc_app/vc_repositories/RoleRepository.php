<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class RoleRepository extends Repository
{
    protected $table = 'vc_roles';

    public function findByName($name)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE name = :name LIMIT 1");
        $stmt->execute(['name' => $name]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}