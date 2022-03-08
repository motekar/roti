<?php

namespace Motekar;

class Roti
{
    private static $_instance = null;
    private static $_useCache = true;

    private function __construct() { }

    public static function getInstance(): Roti
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public static function url($path = '/', $params = [])
    {
        $base_url = dirname($_SERVER['SCRIPT_NAME']);

        if ($path == '/') return $base_url;
        $params_str = !count($params) ? '' : '&' . http_build_query($params);
        return self::trailingslashit($base_url) . '?' . ltrim($path, '/') . $params_str;
    }

    public function useCache($mode = true): Roti
    {
        self::$_useCache = $mode;

        return $this;
    }

    public function run($route_path = 'routes/')
    {
        // trailingslashit
        $route_path = self::trailingslashit($route_path);

        $current_route = empty($_GET) ? 'index' : array_keys($_GET)[0];

        // first _GET parameter has value, set index as current_route
        if (!empty($_GET[$current_route])) {
            $current_route = 'index';
        }

        $routes = self::prepareRoutes($route_path);

        $matches = [];
        $route_file = '';

        // try match with 'index'
        foreach ($routes as $route => $file) {
            if (preg_match(
                    "#$route#",
                    $current_route . 'index',
                    $matches
                )) {
                $route_file = $file;
                $_GET += $matches;
                $_GET['route_pattern'] = $route;
                break;
            }
        }

        if ($route_file) return include $route_file;

        foreach ($routes as $route => $file) {
            if (preg_match("#$route#", $current_route, $matches)) {
                $route_file = $file;
                $_GET += $matches;
                $_GET['route_pattern'] = $route;

                break;
            }
        }

        if ($route_file) return include $route_file;

        http_response_code(404);
        include $route_path . '404.php';
    }

    private static function prepareRoutes($route_path, $cache_path = null) {
        $route_files = glob($route_path . '{*,*/*,*/*/*,*/*/*/*}.php', GLOB_BRACE);

        $cache_path = $cache_path ?? $route_path;
        $checksum = md5(json_encode($route_files));
        $cache_file = $cache_path . ".route-cache-{$checksum}.php";

        // Use cached data
        if (self::$_useCache && file_exists($cache_file)) {
            return include $cache_file;
        }

        $routes = [];

        foreach ($route_files as $filename) {
            $route = preg_replace('/^routes\//', '', $filename);

            if (
                preg_match('/^_/', $route) ||
                preg_match('/\/_/', $route)
            ) continue;

            $route = preg_replace('/\.php$/', '', $route);
            $route = preg_replace('#^' . preg_quote($route_path) . '#', '', $route);

            // $route = preg_replace( '/\[(\w+)\]/' , '(?<$1>[^/]+)' , $route );

            $route = preg_replace_callback('/\[(\w+)\]/', function($matches) {
                [$name, $rule] = self::parseRouteParam($matches[1]);

                $regex = $rule == 'num' ? '[0-9]+' : '[^/]+';
                return "(?<$name>$regex)";
            }, $route);

            $routes["^$route\$"] = realpath($filename);
        }

        // prioritize index routes
        $index_routes = [];
        $other_routes = [];
        foreach ($routes as $route => $file) {
            if (preg_match('#index#', $route)) {
                $index_routes[$route] = $file;
            } else {
                $other_routes[$route] = $file;
            }
        }
        $routes = $index_routes + $other_routes;

        // Delete old caches
        $cache_files = glob($cache_path . '.route-cache-*');
        foreach ($cache_files as $file) unlink($file);

        // Write cache file
        file_put_contents($cache_file, '<?php return ' . var_export($routes, true) . ';');

        return $routes;
    }

    private static function parseRouteParam($param)
    {
        $arr = explode('__', $param);

        if (count($arr) == 2) return [$arr[0], $arr[1]];

        return [$arr[0], $arr[0]];
    }

    private static function trailingslashit($string)
    {
        return rtrim( $string, '/\\' ) . '/';
    }
}
