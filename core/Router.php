<?php
/**
 * Router Class
 * Handles URL routing and dispatching
 */

class Router {
    private $routes = [];
    private $middlewares = [];

    public function get($path, $handler, $middleware = []) {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post($path, $handler, $middleware = []) {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put($path, $handler, $middleware = []) {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete($path, $handler, $middleware = []) {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    private function addRoute($method, $path, $handler, $middleware = []) {
        $path = $this->normalizePath($path);
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    private function normalizePath($path) {
        return '/' . trim($path, '/');
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getCurrentUri();

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $path => $route) {
                $params = $this->matchRoute($path, $uri);
                if ($params !== false) {
                    // Execute middleware
                    foreach ($route['middleware'] as $middleware) {
                        $this->executeMiddleware($middleware);
                    }

                    // Execute handler
                    $this->executeHandler($route['handler'], $params);
                    return;
                }
            }
        }

        // No route found
        $this->handle404();
    }

    private function getCurrentUri() {
        $uri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];

        // Remove script name from URI
        if (strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        } elseif (strpos($uri, dirname($scriptName)) === 0) {
            $uri = substr($uri, strlen(dirname($scriptName)));
        }

        return $this->normalizePath($uri);
    }

    private function matchRoute($routePath, $requestUri) {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($requestUri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $params = [];
        for ($i = 0; $i < count($routeParts); $i++) {
            if (preg_match('/^\{(.+)\}$/', $routeParts[$i], $matches)) {
                $params[$matches[1]] = $uriParts[$i];
            } elseif ($routeParts[$i] !== $uriParts[$i]) {
                return false;
            }
        }

        return $params;
    }

    private function executeMiddleware($middleware) {
        if (is_callable($middleware)) {
            call_user_func($middleware);
        } elseif (is_string($middleware)) {
            $middlewareClass = new $middleware();
            $middlewareClass->handle();
        }
    }

    private function executeHandler($handler, $params = []) {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
        } elseif (is_string($handler)) {
            list($controller, $method) = explode('@', $handler);
            $controllerClass = new $controller();
            call_user_func_array([$controllerClass, $method], $params);
        }
    }

    private function handle404() {
        http_response_code(404);
        echo '404 - Page Not Found';
    }

    public function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }
}
?>