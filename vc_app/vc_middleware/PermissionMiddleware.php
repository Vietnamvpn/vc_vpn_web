<?php

namespace VcApp\VcMiddleware;

class PermissionMiddleware
{
    /**
     * Kiểm tra quyền hạn chi tiết (Granular Permissions)
     * 
     * @param string $permission Quyền cần kiểm tra
     */
    public function handle($permission = '')
    {
        $role = $_SESSION['admin_role'] ?? $_SESSION['staff_role'] ?? '';
        
        // Nếu là super_admin thì bỏ qua kiểm tra
        if ($role === 'super_admin') {
            return;
        }

        // Kiểm tra phân quyền bổ sung tại đây nếu cần
        if (!empty($permission)) {
            $permissions = $_SESSION['permissions'] ?? [];
            if (!in_array($permission, $permissions)) {
                http_response_code(403);
                exit('403 Forbidden: Bạn không có quyền thực hiện hành động này.');
            }
        }
    }
}