<?php

namespace app\models;

use app\models\core\BaseModel;

class News extends BaseModel
{
    protected static $table_name = 'news';

    protected static $public_fields = ['id', 'title', 'description', 'content', 'thumb', 'created_at', 'updated_at', 'is_published'];
    protected static $private_fields = [];

    public $id;
    public $title;
    public $description;
    public $content;
    public $thumb;
    public $created_at;
    public $updated_at;
    public $is_published;

    public function get_thumb_url_attribute()
    {
        global $SITE_URL;

        $src = $this->thumb;
        return "$SITE_URL$src";
    }

    public function get_date_formatted_attribute()
    {
        $date = strtotime($this->created_at);
        return date('d.m.Y', $date);
    }

    public function get_time_formatted_attribute()
    {
        $date = strtotime($this->created_at);
        return date('H:i', $date);
    }

    public function get_reading_time_attribute()
    {
        // Calculate approximate reading time (average reading speed is 200 words per minute)
        $wordCount = str_word_count($this->content);
        $minutes = ceil($wordCount / 200);
        return $minutes;
    }

    public static function create($data)
    {
        if (isset($data['thumb'])) {
            $uploaded = static::upload_image($data['thumb']);
            if ($uploaded) {
                $data['thumb'] = $uploaded;
            } else {
                unset($data['thumb']);
            }
        }

        return parent::create($data);
    }

    public function update($data)
    {
        if (isset($data['thumb'])) {
            $uploaded = static::upload_image($data['thumb']);
            if ($uploaded) {
                $data['thumb'] = $uploaded;
            } else {
                unset($data['thumb']);
            }
        }

        return parent::update($data);
    }

    public static function uploadContentImage($file)
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $uploaded = static::upload_image($file, '/uploads/news/content/');
        return $uploaded ? $uploaded : false;
    }
}
