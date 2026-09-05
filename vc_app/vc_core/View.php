<?php

namespace VcCore;

class View
{
    public function renderView($view, $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->renderLayout();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function renderLayout()
    {
        $layout = 'main';
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            $layout = 'admin';
        }
        
        $layoutPath = __DIR__ . "/../../vc_views/vc_layouts/{$layout}.php";
        if (!file_exists($layoutPath)) {
            $layoutPath = __DIR__ . "/../../vc_views/vc_layouts/main.php";
        }

        ob_start();
        require_once $layoutPath;
        return ob_get_clean();
    }

    protected function renderOnlyView($view, $params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        
        $viewPath = __DIR__ . "/../../vc_views/{$view}.php";
        if (!file_exists($viewPath)) {
            http_response_code(404);
            exit("404 Not Found: View '{$view}' not found.");
        }

        ob_start();
        require_once $viewPath;
        return ob_get_clean();
    }
}