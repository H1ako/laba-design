<?php

namespace app\controllers\core;

use app\models\core\Collection;
use Router;

abstract class Controller
{
    protected static $model;


    public static function redirect($route) {
        return Router::redirect_to($route);
    }

    protected static function get_model_instance($raw_instance)
    {
        if ($raw_instance instanceof static::$model) {
            return $raw_instance;
        }

        return new static::$model->get_by_id($raw_instance);
    }

    public static function list(Collection $collection)
    {
        $json = $collection->json();

        static::json_response($json);
    }


    public static function get($raw_instance)
    {
        $instance = static::get_model_instance($raw_instance);
        if ($instance === null) {
            return static::json_response(static::response_404());
        }

        return static::json_response($instance->json());
    }

    protected static function response_404()
    {
        return static::response_error(404, 'Not found');
    }

    protected static function response_error($code, $message)
    {
        http_response_code($code);

        $response = ['status' => 'error', 'code' => $code, 'message' => $message];

        static::json_response(json_encode($response));
    }

    protected static function response_success($data = [], $message = 'success', $code = 200)
    {
        http_response_code($code);

        $response = ['status' => 'success', 'code' => $code, 'message' => $message, 'data' => $data];

        static::json_response(json_encode($response));
    }

    protected static function json_response($json)
    {
        header('Content-Type: application/json');
        echo $json;
        exit;
    }

    protected static function get_post_data($fields)
    {
        $data = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            foreach ($fields as $form_name => $field) {
                $data[$field] = static::get_post_field($form_name);
            }
        } elseif (strpos($contentType, 'multipart/form-data') !== false) {
            foreach ($fields as $form_name => $field) {
                $data[$field] = static::get_form_field($form_name);
            }
        }

        return $data;
    }

    protected static function get_post_field($field, $default=null)
    {
        return (isset($_POST[$field]) && !empty($_POST[$field])) ? $_POST[$field] : $default;
    }

    protected static function get_query_field($field, $default=null)
    {
        return (isset($_GET[$field]) && !empty($_GET[$field])) ? $_GET[$field] : $default;
    }

    protected static function get_form_field($field)
    {
        if (isset($_FILES[$field])) {
            $file = $_FILES[$field];
            if (is_uploaded_file($file['tmp_name'])) {
                return [
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'size' => $file['size'],
                    'tmp_name' => $file['tmp_name'],
                    'error' => $file['error'],
                ];
            } else {
                return null;
            }
        }

        return static::get_post_field($field);
    }

    protected static function validate_data($data, $rules)
    {
        foreach ($rules as $field => $rule) {
            $value = $data[$field];
            if (!static::validate($value, $rule)) {
                return false;
            };
        }

        return true;
    }

    protected static function validate($value, $rule)
    {
        // rule can be 'required|string|int|file|email|phone_number|datetime|min:{n}|max:{n}'
        $parts = explode('|', $rule);
        $is_required = in_array('required', $parts);

        // If the value is not required and empty, skip all other checks
        if (!$is_required && empty($value)) {
            return true;
        }

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part === 'required' && (empty($value) && $value !== '0')) return false;
            if ($part === 'string' && !is_string($value)) return false;
            if ($part === 'int' && !is_numeric($value)) return false;
            if ($part === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) return false;
            if ($part === 'phone_number') {
                $cleaned = preg_replace('/[\+\(\)\s\-]/', '', $value);
                if (!ctype_digit($cleaned) || strlen($cleaned) < 7 || strlen($cleaned) > 15) return false;
            }
            if ($part === 'datetime' && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $value)) return false;
            if (strpos($part, 'min:') !== false && strlen($value) < substr($part, 4)) return false;
            if (strpos($part, 'max:') !== false && strlen($value) > substr($part, 4)) return false;
            if ($part === 'file') {
                if (!is_array($value) || !isset($value['name'], $value['type'], $value['size'], $value['tmp_name'], $value['error'])) {
                    return false;
                }

                if ($value['error'] !== UPLOAD_ERR_OK) {
                    return false;
                }
            }
        }

        return true;
    }

    protected static function view($view, $data = [])
    {
        Router::render($view, $data);
        exit;
    }
}
