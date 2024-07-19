<?php 

namespace Framework;

class Router {
    protected $routes = [];

    public function registerRoute($method, $uri, $action) {
        list($controller, $controllerMethod) = explode('@', $action);
        

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod
        ];
    }

    /**
     * Add A GET Route
     * @param string $uri
     * @param string $controller
     * @return void
     */

     public function get($uri, $controller) {
        $this->registerRoute('GET', $uri, $controller);
     }

     /**
     * Add A POST Route
     * @param string $uri
     * @param string $controller
     * @return void
     */

     public function post($uri, $controller) {
        $this->registerRoute('POST', $uri, $controller);
     }

     /**
     * Add A PUT Route
     * @param string $uri
     * @param string $controller
     * @return void
     */

     public function put($uri, $controller) {
        $this->registerRoute('PUT', $uri, $controller);
     }

     /**
     * Add A DELETE Route
     * @param string $uri
     * @param string $controller
     * @return void
     */

     public function delete($uri, $controller) {
        $this->registerRoute('DELETE', $uri, $controller);
     }
     /**
      * Route the request
      * @param string $uri
      * @param string $controller
      *@return void
      */

     /**
      * Load error page
      * @param int httpCode
      * @return void
      */

     public function error($httpCode = 404) {
        http_response_code($httpCode);
        loadView("error/{$httpCode}");
        exit;
     } 
      
     public function route($uri, $method){
        foreach($this->routes as $route) {
            if($route['uri'] === $uri && $route['method'] === $method) {
                // Extract controller and controller method
                $controller = 'App\\controllers\\' . $route['controller'];
                $controllerMethod = $route['controllerMethod'];


                // instantiate the controller and call the method 
                $controllerInstance = new $controller();
                $controllerInstance->$controllerMethod();
                return;
            }
        }
        $this->error();
     }
}