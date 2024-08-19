<?php 

namespace Framework;

use App\controllers\ErrorController;

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
      
     public function route($uri){
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Check for _method input 
        if($requestMethod === 'POST' && isset($_POST['_method'])) {
         // Override the request method with the value of method 
         $requestMethod = strtoupper($_POST['_method']);
        }

        foreach($this->routes as $route) {
            
         
         // Split current URI into segment 
         $uriSegments = explode('/', trim($uri, '/'));
         
         // Split the route  URI into segments
         $routeSegments = explode('/', trim($route['uri'], '/'));

         $match = true;

         // Check if the number of segments matches 
         if(count($uriSegments) === count($routeSegments) && strtoupper($route['method'] === $requestMethod)) {
            $params = [];

            $match = true; 
            for($i = 0; $i < count($uriSegments); $i++) {
               // If the URI's do not match and there is no param
               if($routeSegments[$i]!== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                  $match = false;
                  break;
               }
               // check for the param and add to the $params array
               if(preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                  $params[$matches[1]] = $uriSegments[$i];
               }
            }
            if($match) {
               // Extract controller and controller method
               $controller = 'App\\controllers\\' . $route['controller'];
               $controllerMethod = $route['controllerMethod'];


               // instantiate the controller and call the method 
               $controllerInstance = new $controller();
               $controllerInstance->$controllerMethod($params);
               return;
            }
         }
           /* $requestMethod = $_SERVER['REQUEST_METHOD'];

            if($route['uri'] === $uri && $route['method'] === $method) {
                // Extract controller and controller method
                $controller = 'App\\controllers\\' . $route['controller'];
                $controllerMethod = $route['controllerMethod'];


                // instantiate the controller and call the method 
                $controllerInstance = new $controller();
                $controllerInstance->$controllerMethod();
                return;
            } */
        }
        ErrorController::notFound();
   }
}