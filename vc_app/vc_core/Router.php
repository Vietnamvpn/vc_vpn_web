<?php

namespace VcCore;

class Router
{
    protected $routes = [];
    protected $currentMiddleware = [];

    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    protected function addRoute($method, $uri, $action)
    {
        // Chuẩn hóa đường dẫn URI
        $uri = trim($uri, '/');
        $this->routes[$method][$uri] = [
            'action'     => $action,
            'middleware' => $this->currentMiddleware
        ];
        // Đặt lại mảng middleware sau khi gán cho route hiện tại
        $this->currentMiddleware = [];
    }

    public function middleware($middleware)
    {
        $this->currentMiddleware[] = $middleware;
        return $this;
    }

    public function dispatch($uri, $method)
    {
        // Chuẩn hóa URI hiện tại từ request
        $uri = trim($uri, '/');

        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];

            // Thực thi các lớp Middleware bảo vệ trước khi gọi Controller
            foreach ($route['middleware'] as $middleware) {
                $middlewareClass = "VcApp\\VcMiddleware\\" . $middleware;
                if (class_exists($middlewareClass)) {
                    $middlewareInstance = new $middlewareClass();
                    if (method_exists($middlewareInstance, 'handle')) {
                        $middlewareInstance->handle();
                    }
                }
            }

            $action = $route['action'];

            // Nếu action là một Closure ( hàm vô danh )
            if (is_callable($action)) {
                return call_user_func($action);
            }

            // Nếu action là dạng ['ControllerName', 'methodName'] hoặc chuỗi 'ControllerName@methodName'
            if (is_string($action)) {
                $action = explode('@', $action);
            }

            if (is_array($action)) {
                $controllerName = "VcApp\\VcControllers\\" . $action[0];
                $methodName = $action[1];

                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $methodName)) {
                        return $controller->$methodName();
                    }
                }
            }

            http_response_code(404);
            exit('404 Not Found: Controller or Action does not exist.');
        }

        http_response_code(404);
        exit('404 Not Found: Route not found.');
    }
}