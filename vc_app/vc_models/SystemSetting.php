<?php
namespace VcApp\VcModels;
use VcCore\Model;

class SystemSetting extends Model {
    protected $table = 'vc_system_settings';
    protected $primaryKey = 'setting_key';
}