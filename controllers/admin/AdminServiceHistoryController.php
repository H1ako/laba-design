<?php

namespace app\controllers\admin;

use app\controllers\core\Controller;
use app\models\ServiceHistory;
use app\models\Session;

class AdminServiceHistoryController extends Controller
{
    protected static $model = ServiceHistory::class;


    public static function create()
    {
        $session = Session::get();
        $admin = $session->user->admin;

        $data = static::get_post_data([
            'service_id' => 'service_id',
            'initial_price' => 'initial_price',
            'total_price' => 'total_price',
            'status' => 'status',
            'phone_number' => 'phone_number',
            'address' => 'address',
            'final_date' => 'final_date',
            'contact_datetime' => 'contact_datetime',
        ]);
        $is_validated = static::validate_data($data, [
            'service_id' => 'required|int',
            'initial_price' => 'required|int',
            'total_price' => 'required|int',
            'status' => 'required|string',
            'phone_number' => 'required|phone_number',
            'address' => 'string',
            'final_date' => 'string',
            'contact_datetime' => 'string',
        ]);
        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $service_history = $admin->create_service_history($data);

        if ($service_history) {
            return static::response_success([
                'service_history' => $service_history->to_array(),
            ], 'Service history created successfully');
        }

        return static::response_error(502, 'Failed to create service history');
    }

    public static function edit($id)
    {
        $session = Session::get();
        $admin = $session->user->admin;

        $data = static::get_post_data([
            'service_id' => 'service_id',
            'initial_price' => 'initial_price',
            'total_price' => 'total_price',
            'status' => 'status',
            'phone_number' => 'phone_number',
            'address' => 'address',
            'final_date' => 'final_date',
            'contact_date' => 'contact_datetime',
        ]);
        $is_validated = static::validate_data($data, [
            'service_id' => 'required|int',
            'initial_price' => 'required|int',
            'total_price' => 'required|int',
            'status' => 'required|string',
            'phone_number' => 'required|phone_number',
            'address' => 'string',
            'final_date' => 'datetime',
            'contact_datetime' => 'datetime',
        ]);
        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $service_history = $admin->update_service_history_by_id($id, $data);

        return static::response_success([
            'service_history' => $service_history->to_array(),
        ]);
    }

    public static function delete($id)
    {
        $session = Session::get();
        $admin = $session->user->admin;

        if ($admin->delete_service_history_by_id($id)) {
            return static::response_success([], 'Service history deleted successfully');
        }

        return static::response_error(502, 'Failed to delete service history');
    }
}
