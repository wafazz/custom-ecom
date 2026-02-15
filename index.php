<?php

require __DIR__ . '/vendor/autoload.php';

require_once("config/mainConfig.php");
require_once("config/function.php");
require_once("config/sales-compare.php");
require_once("config/ticket-function.php");
require_once("route/routes.php");


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Check if the route exists
if (isset($routes[$method])) {
    $matched = false;

    foreach ($routes[$method] as $routePattern => $handler) {
        // Convert route pattern to a regular expression, replacing placeholders with match groups
        $regexPattern = preg_replace('/:\w+/', '(\w+)', $routePattern);
        $regexPattern = "#^" . $regexPattern . "$#";

        // Check if the current URI matches the pattern
        if (preg_match($regexPattern, $uri, $matches)) {
            $matched = true;

            // Remove the first match (the full string) and keep only parameters
            array_shift($matches);

            // Handle the route
            if (is_callable($handler)) {
                call_user_func_array($handler, $matches);
            } elseif (is_string($handler) && strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);

                // Convert namespace-like strings to file paths
                $controllerPath = str_replace('\\', '/', $controller);
                $controllerFile = __DIR__ . "/controller/$controllerPath.php";

                if (file_exists($controllerFile)) {
                    require_once $controllerFile;

                    $fullyQualifiedController = str_replace('/', '\\', $controller);
                    if (class_exists($fullyQualifiedController)) {
                        $instance = new $fullyQualifiedController();

                        if (method_exists($instance, $method)) {
                            // Pass matched parameters to the controller method
                            call_user_func_array([$instance, $method], $matches);
                        } else {
                            http_response_code(500);
                            echo "Method $method not found in controller $controller.";
                        }
                    } else {
                        http_response_code(500);
                        echo "Controller class $fullyQualifiedController not found.";
                    }
                } else {
                    http_response_code(404);
                    echo "Controller file $controllerPath.php not found.";
                }
            } else {
                http_response_code(500);
                echo "Invalid route handler.";
            }

            break; // Exit loop once a match is found
        }
    }

    if (!$matched) {
        http_response_code(404);
        echo "Page not found.";
    }
} else {
    http_response_code(404);
    echo "Page not found.";
}