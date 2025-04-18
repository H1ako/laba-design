<?php

namespace app\controllers;

use app\controllers\core\Controller;
use app\models\News;
use app\models\Session;

class NewsController extends Controller
{
    /**
     * Display a listing of the news.
     */
    public static function index()
    {

        $page = max(1, intval(static::get_query_field('page') ?? 1));
        $per_page = 8; // Number of news per page

        $total_news = News::where('is_published', '=', 1)->get()->count();
        $total_pages = ceil($total_news / $per_page);

        $news = News::where('is_published', '=', 1)
            ->order_by('created_at', 'DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page)
            ->get();

        return static::view('views/news.php', [
            'news' => $news,
            'total_news' => $total_news,
            'current_page' => $page,
            'total_pages' => $total_pages
        ]);
    }

    /**
     * Display the specified news article.
     */
    public static function show($id)
    {
        $session = Session::get();
        $news = News::get_by_id($id);

        if (!$news || !$news->is_published) {
            return static::redirect('/news');
        }

        // Get related news (e.g., recent news excluding current one)
        $related_news = News::where('is_published', '=', 1)
            ->where('id', '!=', $id)
            ->order_by('created_at', 'DESC')
            ->limit(4)
            ->get();

        return static::view('views/news-single.php', [
            'news' => $news,
            'related_news' => $related_news
        ]);
    }
}
