<?php

return [
    [
        'plan' => [
            'name' => 'VIP Monthly Plan',
            'slug' => 'vip-monthly',
            'description' => 'Gói VIP 1 tháng dung lượng 100GB cao cấp',
            'duration_days' => 30,
            'traffic_limit_bytes' => 107374182400,
            'device_limit' => 3,
            'speed_limit_mbps' => 100,
            'max_connections' => 3,
            'status' => 'active',
            'sort_order' => 1,
        ],
        'prices' => [
            ['currency' => 'USD', 'amount' => 5.00, 'billing_period' => 'monthly', 'is_active' => true],
            ['currency' => 'VND', 'amount' => 120000.00, 'billing_period' => 'monthly', 'is_active' => true],
        ],
        'features' => [
            ['feature_key' => 'speed', 'feature_value' => 'Up to 100 Mbps', 'sort_order' => 1],
            ['feature_key' => 'support', 'feature_value' => '24/7 Priority Support', 'sort_order' => 2],
        ],
    ],
    [
        'plan' => [
            'name' => 'VIP Yearly Plan',
            'slug' => 'vip-yearly',
            'description' => 'Gói VIP 1 năm dung lượng 1TB cao cấp',
            'duration_days' => 365,
            'traffic_limit_bytes' => 1099511627776,
            'device_limit' => 5,
            'speed_limit_mbps' => 300,
            'max_connections' => 5,
            'status' => 'active',
            'sort_order' => 2,
        ],
        'prices' => [
            ['currency' => 'USD', 'amount' => 50.00, 'billing_period' => 'yearly', 'is_active' => true],
            ['currency' => 'VND', 'amount' => 1200000.00, 'billing_period' => 'yearly', 'is_active' => true],
        ],
        'features' => [
            ['feature_key' => 'speed', 'feature_value' => 'Up to 300 Mbps', 'sort_order' => 1],
            ['feature_key' => 'support', 'feature_value' => 'VIP Dedicated Support', 'sort_order' => 2],
        ],
    ],
];