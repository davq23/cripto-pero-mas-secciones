<?php

namespace routes;

class Router
{
    private $rules = [
        '(:num)' => 'utils\\RouteRules::is_natural',
        '(:format)' => 'utils\\RouteRules::is_format'
    ];

    private $routes;

    private $notFound;

    public function __construct()
    {
        $this->routes = [
            'GET' => [],
            'POST' => [],
            'PUT' => [],
            'DELETE' => [],
        ];

        $this->notFound =  function() {
            http_response_code(404);
            echo 'Not Found';
        };
    }

    public function getNotFound() {
        return $this->notFound;
    }

    public function endsWith($haystack, $needle) {
        $length = strlen($needle);
        return $length > 0 ? substr($haystack, -$length) === $needle : true;
    }

    private static function registerRoute(array &$routeArray, string $pattern, $callback)
    {
        if ($pattern === '/') {
            $routeArray['/'] = $callback;
            return;
        }

        $patternParts = explode('/', $pattern);

        $currentRoute = null;

        $currentRoute = &$routeArray;

        for ($i=0; $i < count($patternParts); $i++) {
            if ($patternParts[$i] === '')
                $patternParts[$i] = '/';

            if ($i !== count($patternParts) - 1) {
                if (!isset($currentRoute[$patternParts[$i]]))
                    $currentRoute[$patternParts[$i]] = [];

                $currentRoute = &$currentRoute[$patternParts[$i]];
                continue;
            }

            $currentRoute[$patternParts[$i]] = $callback;
        }
    }

    public function run()
    {
        $path = str_replace('/php/public', '', $_SERVER['REQUEST_URI']);

        if (!$this->endsWith($path, '/')) $path .= '/';
        
        $patternParts = explode('/', $path);

        if ($patternParts[0] === '')
            unset($patternParts[0]);

        if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
            $this->getNotFound()();
            return;
        }

        $currentRoute = &$this->routes[$_SERVER['REQUEST_METHOD']];
        $args = [];

        foreach ($patternParts as &$part) {
            if ($part === '')
                $part = '/';

            if (!isset($currentRoute[$part])) {
                $ok = false; 
                $rule = '';
                $patternRules = array_intersect_key($currentRoute, $this->rules);

                if (count($patternRules) > 0) {
                    foreach ($patternRules as $patternRule => $val) {
                        $ok = call_user_func($this->rules[$patternRule], $part);  
                        if ($ok) {
                            $rule = $patternRule;
                            break;
                        }
                    }
                } 

                if (!$ok) break;

                $args[] = $part;

                $part = $rule;
            }

            if (is_array($currentRoute[$part])) {
                $currentRoute = &$currentRoute[$part];
                continue;
            }

            $classParts = explode('::', $currentRoute[$part], 2);

            if (!class_exists($classParts[0])) {
                break;
            }

            $controllerClass = new $classParts[0];

            if (!method_exists($controllerClass, $classParts[1])) {
                break;
            }

            call_user_func([$controllerClass, $classParts[1]], ...$args);
            return;
        }

        $this->getNotFound()();
    }

    public function setNotFound($callback) {
        $this->notFound = $callback;
    }

    /**
     * Registers a GET route
     *
     * @param string $pattern
     * @param string $callback
     * @return void
     */
    public function GET(string $pattern, string $callback)
    {
        self::registerRoute($this->routes['GET'], $pattern, $callback);
    }

    /**
     * Registers POST route
     *
     * @param string $pattern
     * @param string $callback
     * @return void
     */
    public function POST(string $pattern, string $callback)
    {
        self::registerRoute($this->routes['POST'], $pattern, $callback);
    }

    /**
     * Registers PUT route
     *
     * @param string $pattern
     * @param string $callback
     * @return void
     */
    public function PUT(string $pattern, string $callback)
    {
        self::registerRoute($this->routes['PUT'], $pattern, $callback);
    }

    /**
     * Registers DELETE route
     *
     * @param string $pattern
     * @param string $callback
     * @return void
     */
    public function DELETE(string $pattern, string $callback)
    {
        self::registerRoute($this->routes['DELETE'], $pattern, $callback);
    }
}
