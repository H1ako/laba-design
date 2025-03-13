<?php

namespace app\models;

use app\models\core\BaseModel;
use app\models\core\Collection;

class ServiceHistory extends BaseModel
{
    protected static $table_name = 'service_history';

    protected static $public_fields = ['initial_price', 'total_price', 'final_date', 'phone_number', 'contact_datetime', 'address'];
    protected static $private_fields = ['id', 'updated_at', 'created_at', 'service_id', 'user_id', 'status'];

    public $initial_price;
    public $total_price;
    public $final_date;
    public $contact_datetime;
    public $phone_number;
    public $address;
    protected $service_id;
    protected $user_id;
    protected $status;

    protected $service_data;
    protected $images_data;


    public function get_service_attribute()
    {
        if (!isset($this->service_data)) {
            $this->service_data = Service::get_by_id($this->service_id);
        }
        return $this->service_data;
    }


    public function get_images_attribute()
    {
        if (!isset($this->images_data)) {
            $this->images_data = ServiceHistoryImage::where('service_history_id', '=', $this->id)->get();
        }
        return $this->images_data;
    }


    public function get_status_attribute()
    {
        return $this->status;
    }


    public function set_initial()
    {
        $this->set_status('initial');
    }


    public function set_in_work()
    {
        $this->set_status('working');
    }


    public function finish()
    {
        $this->set_status('success');
    }


    public function cancel()
    {
        $this->set_status('canceled');
    }


    public function get_is_initial_attribute()
    {
        return $this->status === 'initial';
    }


    public function get_is_in_work_attribute()
    {
        return $this->status === 'working';
    }


    public function get_is_finished_attribute()
    {
        return $this->status === 'success';
    }


    public function get_is_canceled_attribute()
    {
        return $this->status === 'canceled';
    }

    public function get_contact_datetime_formatted_attribute() {
        return static::format_date($this->contact_datetime);
    }
    
    public function get_final_date_formatted_attribute() {
        return static::format_date($this->final_date);
    }

    public function get_total_price_formatted_attribute() {
        return static::format_price($this->total_price);
    }

    protected function set_status($status)
    {
        if (!in_array($status, ['initial', 'working', 'success', 'canceled'])) return false;

        $this->status = $status;
    }

    public static function create($data)
    {
        if (isset($data['service_id'])) {
            $service = Service::get_by_id($data['service_id']);
            $data['initial_price'] = $service->base_price;
            $data['total_price'] = $service->base_price;
        }

        return parent::create($data);
    }
}
