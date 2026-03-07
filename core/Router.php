<?php

class Router
{
    private $routes = [];
    
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base folder from URI if needed
        $basePath = '/ewgs';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Normalize: strip trailing slash except for root '/'
        $uri = rtrim($uri, '/') ?: '/';
        
        // Check for exact match first
        if (isset($this->routes[$method][$uri])) {
            $this->executeCallback($this->routes[$method][$uri]);
            return;
        }
        
        // Check for dynamic routes
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $callback) {
                $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '([a-zA-Z0-9_-]+)', $route);
                $pattern = '#^' . $pattern . '$#';
                
                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // Remove full match
                    $this->executeCallback($callback, $matches);
                    return;
                }
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
    }
    
    private function executeCallback($callback, $params = [])
    {
        if (is_callable($callback)) {
            call_user_func_array($callback, $params);
        } elseif (is_string($callback)) {
            // Format: "Controller@method"
            list($controller, $method) = explode('@', $callback);
            $controllerInstance = new $controller();
            call_user_func_array([$controllerInstance, $method], $params);
        }
    }
}