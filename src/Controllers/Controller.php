<?php
namespace Simcify\Controllers;

abstract class Controller {
    
    /**
     * Load route specific middleware
     * 
     * @param   string  $middleware
     * @return  void
     */
    protected function middleware($middleware) {
        $middlewares = container()->get('middlware');
        if (isset($middlewares[$middleware])) {
            $class = new $middlewares[$middleware];
            $class->handle(request());
        }
    }
}
