<?php

return [
    ['key' => 'app_name', 'value' => 'VC VPN Commerce', 'type' => 'string', 'is_secret' => false],
    ['key' => 'app_url', 'value' => 'https://vpn.example.com', 'type' => 'string', 'is_secret' => false],
    ['key' => 'currency_default', 'value' => 'USD', 'type' => 'string', 'is_secret' => false],
    ['key' => 'timezone', 'value' => 'UTC', 'type' => 'string', 'is_secret' => false],
    ['key' => 'vpn_default_core', 'value' => 'singbox', 'type' => 'string', 'is_secret' => false],
    ['key' => 'vpn_sub_base_url', 'value' => 'https://vpn.example.com/vc_subscription', 'type' => 'string', 'is_secret' => false],
    ['key' => 'smtp_host', 'value' => 'smtp.example.com', 'type' => 'string', 'is_secret' => false],
    ['key' => 'smtp_port', 'value' => '587', 'type' => 'integer', 'is_secret' => false],
    ['key' => 'smtp_user', 'value' => '', 'type' => 'string', 'is_secret' => false],
    ['key' => 'smtp_pass', 'value' => '', 'type' => 'string', 'is_secret' => true],
    ['key' => 'affiliate_commission_default', 'value' => '10', 'type' => 'integer', 'is_secret' => false],
];