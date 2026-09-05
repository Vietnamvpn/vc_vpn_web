<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class AuditRepository extends Repository
{
    protected $table = 'vc_audit_logs';

    public function logAction($userId, $action, $details = '')
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, action, details, created_at) VALUES (:user_id, :action, :details, NOW())");
        return $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'details' => $details
        ]);
    }
}