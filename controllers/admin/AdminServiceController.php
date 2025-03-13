<?php

namespace app\controllers\admin;

use app\controllers\core\Controller;
use app\models\Service;
use app\models\Session;


class AdminServiceController extends Controller
{
    protected static $model = Service::class;


    public static function create()
    {
        $session = Session::get();
        $admin = $session->user->admin;

        $data = static::get_post_data([
            'preview_image' => 'preview_image',
            'name' => 'name',
            'base_price' => 'base_price',
            'base_completion_time' => 'base_completion_time',
            'base_workers_amount' => 'base_workers_amount'
        ]);
        $is_validated = static::validate_data($data, [
            'preview_image' => 'file',
            'name' => 'required|string',
            'base_price' => 'required',
            'base_completion_time' => 'required|int',
            'base_workers_amount' => 'required|int'
        ]);
        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $service = $admin->create_service($data);

        if ($service) {
            return static::response_success([
                'service' => $service->to_array(),
            ], 'Service created successfully');
        }

        return static::response_error(502, 'Failed to create service');
    }

    public static function edit($id)
    {
        $session = Session::get();
        $admin = $session->user->admin;

        $data = static::get_post_data([
            'preview_image' => 'preview_image',
            'name' => 'name',
            'base_price' => 'base_price',
            'base_completion_time' => 'base_completion_time',
            'base_workers_amount' => 'base_workers_amount'
        ]);
        $is_validated = static::validate_data($data, [
            'preview_image' => 'file',
            'name' => 'required|string',
            'base_price' => 'required|int',
            'base_completion_time' => 'required|int',
            'base_workers_amount' => 'required|int'
        ]);
        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $service = $admin->update_service_by_id($id, $data);

        return static::response_success([
            'service' => $service->to_array(),
        ]);
    }

    public static function delete($id) {
        $session = Session::get();
        $admin = $session->user->admin;

        if ($admin->delete_service_by_id($id)) {
            return static::response_success([], 'Service deleted successfully');    
        }

        return static::response_error(502, 'Failed to delete service');
    }
}
