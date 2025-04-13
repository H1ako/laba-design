<?php

namespace app\controllers\admin;

use app\controllers\core\Controller;
use app\models\News;
use app\models\Session;

class AdminNewsController extends Controller
{
    /**
     * Display a listing of the news.
     */
    public static function index()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();

        $search = static::get_query_field('search');
        $page = max(1, intval(static::get_query_field('page') ?? 1));
        $per_page = 10;

        $query = News::query();

        if ($search) {
            $query = $query->where_raw(
                "(title LIKE ? OR description LIKE ?)",
                ["%$search%", "%$search%"]
            );
        }

        $total_news = $query->count();
        $total_pages = ceil($total_news / $per_page);

        $news = $query->order_by('created_at', 'DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page)
            ->get();

        return static::view('views/admin/news/index.php', [
            'user' => $session->user,
            'news' => $news,
            'total_news' => $total_news,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'search' => $search
        ]);
    }

    /**
     * Show the form for creating a new news.
     */
    public static function create()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();

        return static::view('views/admin/news/create.php', [
            'user' => $session->user
        ]);
    }

    /**
     * Store a newly created news in storage.
     */
    public static function store()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $data = static::get_post_data([
            'title' => 'title',
            'description' => 'description',
            'content' => 'content',
            'is_published' => 'is_published',
            'thumb' => 'thumb'
        ]);

        $validation_rules = [
            'title' => 'required|string|min:3',
            'description' => 'required|string|min:10',
            'content' => 'required|string|min:50',
            'thumb' => 'required|file'
        ];

        $data['is_published'] = $data['is_published'] ?? 0;

        $is_validated = static::validate_data($data, $validation_rules);

        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $news = News::create($data);

        return static::response_success([
            'message' => 'News created successfully',
            'id' => $news->id,
            'redirect' => '/admin/news/' . $news->id
        ]);
    }

    /**
     * Display the specified news.
     */
    public static function show($id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();
        $news = News::get_by_id($id);

        if (!$news) {
            return static::redirect('/admin/news');
        }

        return static::view('views/admin/news/show.php', [
            'user' => $session->user,
            'news' => $news
        ]);
    }

    /**
     * Show the form for editing the specified news.
     */
    public static function edit($id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();
        $news = News::get_by_id($id);

        if (!$news) {
            return static::redirect('/admin/news');
        }

        return static::view('views/admin/news/edit.php', [
            'user' => $session->user,
            'news' => $news
        ]);
    }

    /**
     * Update the specified news in storage.
     */
    public static function update($id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $news = News::get_by_id($id);

        if (!$news) {
            return static::response_error(404, 'News not found');
        }

        $data = static::get_post_data([
            'title' => 'title',
            'description' => 'description',
            'content' => 'content',
            'is_published' => 'is_published',
            'thumb' => 'thumb'
        ]);

        $validation_rules = [
            'title' => 'required|string|min:3',
            'description' => 'required|string|min:10',
            'content' => 'required|string|min:50'
        ];

        // Only validate thumb if it's provided
        if (isset($data['thumb'])) {
            $validation_rules['thumb'] = 'file';
        }

        $data['is_published'] = $data['is_published'] ?? 0;

        $is_validated = static::validate_data($data, $validation_rules);

        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $news->update($data);
        $news->save();

        return static::response_success([
            'message' => 'News updated successfully',
            'redirect' => '/admin/news/' . $news->id
        ]);
    }

    /**
     * Remove the specified news from storage.
     */
    public static function delete($id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $news = News::get_by_id($id);

        if (!$news) {
            return static::response_error(404, 'News not found');
        }

        $news->delete();

        return static::response_success([
            'message' => 'News deleted successfully'
        ]);
    }

    /**
     * Upload image for rich text editor
     */
    public static function upload_image()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return static::response_error(401, 'Unauthorized');
        }

        
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return static::response_error(400, 'No image uploaded or upload error');
        }
        
        $uploaded = News::uploadContentImage($_FILES['image']);

        if (!$uploaded) {
            return static::response_error(500, 'Failed to upload image');
        }

        global $SITE_URL;
        return static::response_success([
            'url' => $SITE_URL . $uploaded
        ]);
    }
}
