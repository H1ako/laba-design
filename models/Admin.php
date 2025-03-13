<?php

namespace app\models;

use app\models\core\BaseModel;

class Admin extends BaseModel
{
    protected $user;


    public function __construct($user)
    {
        $this->user = $user;
    }

    public function create_service($data)
    {
        return Service::create($data);
    }

    public function update_service_by_id($id, $data)
    {
        if (isset($data['preview_image'])) {
            $uploaded = static::upload_image($data['preview_image']);
            if ($uploaded) {
                $data['preview_image'] = $uploaded;
            } else {
                unset($data['preview_image']);
            }
        }

        $service = Service::get_by_id($id);
        $service->_update_all($data)->save();

        return $service;
    }

    public function delete_service_by_id($id)
    {
        return Service::get_by_id($id)->delete();
    }

    public function create_service_history($data)
    {
        return ServiceHistory::create($data);
    }

    public function update_service_history_by_id($id, $data)
    {
        $service_history = ServiceHistory::get_by_id($id);
        $service_history->_update_all($data)->save();

        return $service_history;
    }

    public function delete_service_history_by_id($id)
    {
        return ServiceHistory::get_by_id($id)->delete();
    }

    public function create_user($data)
    {
        return User::create($data);
    }

    public function update_user_by_id($id, $data)
    {
        $user = User::get_by_id($id);
        $user->_update_all($data);

        if ($data['password']) {
            $user->set_password($data['password']);
        }

        $user->save();

        return $user;
    }

    public function delete_user_by_id($id)
    {
        return User::get_by_id($id)->delete();
    }
}
