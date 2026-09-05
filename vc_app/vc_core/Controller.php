<?php

namespace VcCore;

class Controller
{
    /**
     * Render một tệp giao diện (view) kèm theo dữ liệu truyền vào
     * 
     * @param string $viewPath Đường dẫn tương đối tính từ thư mục vc_views/ (ví dụ: 'vc_admin/dashboard')
     * @param array $data Dữ liệu truyền sang view
     */
    protected function view($viewPath, $data = [])
    {
        // Giải nén mảng dữ liệu thành các biến riêng biệt trong view
        extract($data);

        $file = __DIR__ . '/../../vc_views/' . $viewPath . '.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            http_response_code(404);
            exit("404 Not Found: View file '{$viewPath}.php' does not exist.");
        }
    }

    /**
     * Trả về phản hồi dạng JSON (dùng cho API hoặc AJAX)
     * 
     * @param mixed $data Dữ liệu cần trả về
     * @param int $statusCode Mã trạng thái HTTP (mặc định 200)
     */
    protected function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Chuyển hướng trình duyệt đến một URL khác
     * 
     * @param string $url Đường dẫn cần chuyển hướng
     */
    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
}