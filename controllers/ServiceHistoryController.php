<?php

namespace app\controllers;

use app\controllers\core\Controller;
use app\models\ServiceHistory;
use app\models\Session;

class ServiceHistoryController extends Controller
{
    protected static $model = ServiceHistory::class;

    public static function create()
    {
        $session = Session::get();
        $data = static::get_post_data(['service_id' => 'service_id', 'phone' => 'phone_number', 'address' => 'address', 'datetime' => 'contact_datetime']);
        $is_validated = static::validate_data($data, [
            'service_id' => 'required|string',
            'phone_number' => 'required|string|phone_number',
            'address' => 'string|min:8|max:160',
            'contact_datetime' => 'string|datetime'
        ]);
        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $data['user_id'] = $session->user->get_id();
        $service_history = ServiceHistory::create($data);

        return static::response_success([
            'service_history' => $service_history->to_array()
        ]);
    }


    public static function edit($service_history_id)
    {
        $session = Session::get();
        $service_history = $session->user->service_history->where('id', '=', $service_history_id)->get()->first();
        if ($service_history === null) {
            return static::response_error(404, 'Service not found');
        }

        $data = static::get_post_data(['phone' => 'phone_number', 'address' => 'address', 'datetime' => 'contact_datetime']);
        $is_validated = static::validate_data($data, [
            'phone_number' => 'required|string|phone_number',
            'address' => 'string|min:8|max:160',
            'contact_datetime' => 'string|datetime'
        ]);
        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $service_history->update($data);
        if (!$service_history->save()) {
            return static::response_error(502, 'Failed to update service');
        }

        return static::response_success([
            'service_history' => $service_history->to_array()
        ]);
    }
}
