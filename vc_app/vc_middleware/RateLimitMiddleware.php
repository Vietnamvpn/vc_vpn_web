<?php

namespace VcApp\VcMiddleware;

class RateLimitMiddleware
{
    /**
     * Giới hạn tần suất gửi yêu cầu (Rate Limiting) chống Brute-force / Spam
     * 
     * @param int $maxRequests Số lượng request tối đa cho phép
     * @param int $timeWindow Khoảng thời gian tính bằng giây
     */
    public function handle($maxRequests = 60, $timeWindow = 60)
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = 'rate_limit_' . md5($ip);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'start_time' => time()
            ];
            return;
        }

        $current = time();
        if (($current - $_SESSION[$key]['start_time']) > $timeWindow) {
            $_SESSION[$key] = [
                'count' => 1,
                'start_time' => $current
            ];
            return;
        }

        $_SESSION[$key]['count']++;
        
        if ($_SESSION[$key]['count'] > $maxRequests) {
            http_response_code(429);
            exit('429 Too Many Requests: Bạn đã gửi quá nhiều yêu cầu, vui lòng thử lại sau.');
        }
    }
}