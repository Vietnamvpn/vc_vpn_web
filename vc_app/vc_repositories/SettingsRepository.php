<?php

namespace VcApp\VcRepositories;

use VcCore\Repository;

class SettingsRepository extends Repository
{
    protected $table = 'vc_system_settings';
    protected $primaryKey = 'setting_key';

    public function getByKey($key)
    {
        $stmt = $this->db->prepare("SELECT setting_value FROM {$this->table} WHERE setting_key = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : null;
    }
}