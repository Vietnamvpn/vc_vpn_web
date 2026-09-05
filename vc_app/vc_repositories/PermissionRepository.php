<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class PermissionRepository extends Repository
{
    protected $table = 'vc_permissions';

    public function getByRoleId($roleId)
    {
        $stmt = $this->db->prepare("SELECT p.* FROM vc_permissions p JOIN vc_role_permissions rp ON p.id = rp.permission_id WHERE rp.role_id = :role_id");
        $stmt->execute(['role_id' => $roleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}