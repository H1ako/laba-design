<?php

namespace app\models;

use app\models\core\BaseModel;

class Service extends BaseModel
{
    protected static $table_name = 'services';

    protected static $public_fields = ['name', 'base_price', 'base_completion_time', 'base_workers_amount', 'preview_image'];

    public $name;
    public $preview_image;
    public $base_price;
    public $base_completion_time;
    public $base_workers_amount;

    protected $user_data;


    public function get_preview_image_url_attribute()
    {
        global $SITE_URL;

        $src = $this->preview_image;
        return "$SITE_URL$src";
    }

    public function get_user_attribute()
    {
        if (!isset($this->user_data)) {
            $this->user_data = User::get_by_id($this->user_id);
        }
        return $this->user_data;
    }

    public function get_base_price_formatted_attribute()
    {
        return static::format_price($this->base_price);
    }

    public function get_base_workers_amount_formatted_attribute()
    {
        $workers = $this->base_workers_amount;

        return $workers . ' ' . $this->get_russian_word_form($workers, 'сотрудник', 'сотрудника', 'сотрудников');
    }

    public function get_base_completion_time_formatted_attribute()
    {
        $seconds = $this->base_completion_time;

        // Convert seconds to days and weeks
        $weeks = floor($seconds / (7 * 24 * 60 * 60));
        $days = floor(($seconds % (7 * 24 * 60 * 60)) / (24 * 60 * 60));

        $result = '';

        if ($weeks > 0) {
            $result .= $weeks . ' ' . $this->get_russian_word_form($weeks, 'неделя', 'недели', 'недель');
        }

        if ($days > 0) {
            if ($weeks > 0) {
                $result .= ' и ';
            }
            $result .= $days . ' ' . $this->get_russian_word_form($days, 'день', 'дня', 'дней');
        }

        return $result ?: 'менее дня';
    }

    protected function get_russian_word_form($number, $form1, $form2, $form5)
    {
        $number = abs($number) % 100;
        $n1 = $number % 10;

        if ($number > 10 && $number < 20) {
            return $form5;
        }
        if ($n1 > 1 && $n1 < 5) {
            return $form2;
        }
        if ($n1 == 1) {
            return $form1;
        }
        return $form5;
    }

    public static function create($data)
    {
        if (isset($data['preview_image'])) {
            $uploaded = static::upload_image($data['preview_image']);
            if ($uploaded) {
                $data['preview_image'] = $uploaded;
            } else {
                unset($data['preview_image']);
            }
        }

        return parent::create($data);
    }

    public function update($data)
    {
        if (isset($data['preview_image'])) {
            $uploaded = static::upload_image($data['preview_image']);
            if ($uploaded) {
                $data['preview_image'] = $uploaded;
            } else {
                unset($data['preview_image']);
            }
        }

        return parent::update($data);
    }
}
