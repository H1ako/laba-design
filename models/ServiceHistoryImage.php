<?php

namespace app\models;

use app\models\core\BaseModel;

class ServiceHistoryImage extends BaseModel
{
    protected static $table_name = 'service_history_images';

    
    protected static $public_fields = ['url'];
    protected static $private_fields = ['id', 'updated_at', 'created_at', 'service_history_id'];
    
    public $url;
    protected $service_history_id;

    protected $service_history_data;


    public function get_service_history_attribute()
    {
        if (!isset($this->service_history_data)) {
            $this->service_history_data = ServiceHistory::get_by_id($this->service_history_id);
        }
        return $this->service_history_data;
    }
}

