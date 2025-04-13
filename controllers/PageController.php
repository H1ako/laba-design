<?php

namespace app\controllers;

use app\controllers\core\Controller;
use app\models\CustomPage;

class PageController extends Controller
{
    /**
     * Display a custom page by slug.
     */
    public static function show($slug)
    {
        $page = CustomPage::where('slug', '=', $slug)
            ->where('is_published', '=', 1)
            ->get()
            ->first();

        if (!$page) {
            return static::not_found();
        }

        return static::view('views/page.php', [
            'page' => $page
        ]);
    }
    
    /**
     * Preview a page for admins - includes unpublished pages
     */
    public static function preview($slug)
    {
        // Get page even if unpublished
        $page = CustomPage::where('slug', '=', $slug)->get()->first();
        
        if (!$page) {
            return static::not_found();
        }
        
        return static::view('views/page.php', [
            'page' => $page,
            'is_preview' => true
        ]);
    }
}