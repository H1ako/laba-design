<?php

use app\models\Session;

class Router
{
  protected static $endpoint = '';

  public static function getRoute($path, $data=null) {
    global $SITE_URL;

    $url = $SITE_URL . $path;
    if ($data) {
      $url .= '?' . http_build_query($data);
    }
    return $url;
  }

  public static function set_route_prefix($route)
  {
    static::$endpoint = rtrim($route, '/');
  }

  public static function get($route, $path_to_include)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      static::route($route, $path_to_include);
    }
  }
  public static function post($route, $path_to_include)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      static::route($route, $path_to_include);
    }
  }
  public static function put($route, $path_to_include)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
      static::route($route, $path_to_include);
    }
  }
  public static function patch($route, $path_to_include)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
      static::route($route, $path_to_include);
    }
  }
  public static function delete($route, $path_to_include)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
      static::route($route, $path_to_include);
    }
  }
  public static function any($route, $path_to_include)
  {
    static::route($route, $path_to_include);
  }

  public static function not_found()
  {
    http_response_code(404);
    echo '404 Not Found';
    exit;
  }
  public static function redirect_to($path)
  {
    global $SITE_URL;

    header("Location: $SITE_URL$path");
    exit();
  }

  protected static function validate_csrf()
  {
    $session = Session::get();
    
    $unsafeMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
    
    if (in_array($_SERVER['REQUEST_METHOD'], $unsafeMethods, true)) {
      if ($session->is_authed && $session->user->is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') return true;
      $csrfToken = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

      if (!$session->validate_csrf_token($csrfToken)) {
        http_response_code(403);
        echo 'Invalid CSRF token';
        exit;
      }
    }
  }

  protected static function route($route, $path_to_include)
  {
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
      $_POST = json_decode(file_get_contents('php://input'), true) ?: [];
    }
    
    static::validate_csrf();

    global $DEV_URL_PART;

    if (static::$endpoint) {
      $route = '/' . static::$endpoint . $route;
    }
    $route = $DEV_URL_PART . $route;

    $callback = $path_to_include;
    if (!is_callable($callback)) {
      if (!strpos($path_to_include, '.php')) {
        $path_to_include .= '.php';
      }
    }
    if (is_array($callback)) {
      $callback = function (...$args) use ($callback) {
        [$class, $method] = $callback;

        if (class_exists($class) && method_exists($class, $method)) {
          // Call the method dynamically and pass the arguments
          return call_user_func_array([new $class, $method], $args);
        } else {
          throw new Exception("Class `$class` or method `$method` does not exist.");
        }
      };
    }
    $request_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
    $request_url = rtrim($request_url, '/');
    $request_url = strtok($request_url, '?');
    $route_parts = explode('/', $route);
    $request_url_parts = explode('/', $request_url);

    $parameters = [];
    for ($__i__ = 0; $__i__ < count($route_parts); $__i__++) {
      $route_part = $route_parts[$__i__];
      if (preg_match("/^[%]/", $route_part)) {
        $route_part = ltrim($route_part, '%');
        array_push($parameters, $request_url_parts[$__i__] ?? '');
        $$route_part = $request_url_parts[$__i__] ?? '';
      }
    }
    $route_with_params = vsprintf($route, $parameters);
    if ($route_with_params != $request_url && $route_with_params != $request_url . '/' && $route_with_params . '/' != $request_url) return;

    array_shift($route_parts);
    array_shift($request_url_parts);
    if (isset($route_parts[0]) && $route_parts[0] == '' && count($request_url_parts) == 0) {
      if (is_callable($callback)) {
        $template = static::route_callback($callback, []);
        if (!$template) return;

        $path_to_include = $template;
      }
      static::render($path_to_include);
      exit();
    }
    if (is_callable($callback)) {
      $template = static::route_callback($callback, $parameters);
      if (!$template) return;

      $path_to_include = $template;
    }
    static::render($path_to_include);
    exit();
  }

  public static function render($path_to_include, $data = [])
  {
    if (is_array($data)) {
      extract($data);
    }
    include_once __DIR__ . "/../$path_to_include";
  }

  protected static function route_callback($callback, $parameters)
  {
    $template = call_user_func_array($callback, $parameters);

    if ($template === false) exit();
    if (file_exists(__DIR__ . "/$template")) {
      return $template;
    }

    return false;
  }
}
