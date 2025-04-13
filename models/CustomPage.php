<?php

namespace app\models;

use app\models\core\BaseModel;

class CustomPage extends BaseModel
{
    protected static $table_name = 'custom_pages';

    protected static $public_fields = ['id', 'title', 'slug', 'content', 'meta_description', 'created_at', 'updated_at', 'is_published', 'sort_order'];
    protected static $private_fields = [];

    public $id;
    public $title;
    public $slug;
    public $content;
    public $meta_description;
    public $created_at;
    public $updated_at;
    public $is_published;
    public $sort_order;

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
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200);
        return $minutes;
    }

    public static function create($data)
    {
        // Generate slug from title if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = self::generateSlug($data['title']);
        } else {
            // Clean the provided slug
            $data['slug'] = self::generateSlug($data['slug']);
        }

        // Set default sort order if not provided
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = self::getNextSortOrder();
        }

        // Set default published state if not provided
        if (!isset($data['is_published'])) {
            $data['is_published'] = 0;
        }

        return parent::create($data);
    }

    public function update($data)
    {
        // Update slug only if explicitly provided
        if (isset($data['slug'])) {
            // Clean the provided slug
            $data['slug'] = self::generateSlug($data['slug']);
        }

        return parent::update($data);
    }

    /**
     * Generate a clean slug from a string
     *
     * @param string $text
     * @return string
     */
    protected static function generateSlug($text)
    {
        // Transliterate non-ASCII characters to ASCII
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII', $text);

        // Convert to lowercase
        $text = strtolower($text);

        // Remove special characters and replace spaces with hyphens
        $text = preg_replace('/[^a-z0-9\-]/', '-', $text);

        // Replace multiple hyphens with single hyphen
        $text = preg_replace('/-+/', '-', $text);

        // Remove leading and trailing hyphens
        $text = trim($text, '-');

        // Ensure uniqueness by adding a suffix if needed
        $originalSlug = $text;
        $counter = 1;

        while (self::slugExists($text)) {
            $text = $originalSlug . '-' . $counter++;
        }

        return $text;
    }

    /**
     * Check if a slug already exists in the database
     *
     * @param string $slug
     * @return bool
     */
    protected static function slugExists($slug)
    {
        $page = self::where('slug', '=', $slug)->get()->first();
        return $page !== null;
    }

    /**
     * Get the next available sort order
     *
     * @return int
     */
    protected static function getNextSortOrder()
    {
        $maxOrder = self::select('MAX(sort_order) as max_order')
            ->get()
            ->first();

        return ($maxOrder && isset($maxOrder->max_order)) ? $maxOrder->max_order + 10 : 10;
    }
}
