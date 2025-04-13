<?php

namespace app\controllers\admin;

use app\controllers\core\Controller;
use app\models\CustomPage;
use app\models\Session;

class AdminPagesController extends Controller
{
    /**
     * Display a listing of all pages.
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

        $query = CustomPage::query();

        if ($search) {
            $query = $query->where_raw(
                "(title LIKE ? OR slug LIKE ?)",
                ["%$search%", "%$search%"]
            );
        }

        $total_pages = $query->count();
        $total_pagination_pages = ceil($total_pages / $per_page);

        $pages = $query->order_by('sort_order')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page)
            ->get();

        return static::view('views/admin/pages/index.php', [
            'user' => $session->user,
            'pages' => $pages,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'total_pagination_pages' => $total_pagination_pages,
            'search' => $search
        ]);
    }

    /**
     * Show the form for creating a new page.
     */
    public static function create()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();

        return static::view('views/admin/pages/create.php', [
            'user' => $session->user
        ]);
    }

    /**
     * Store a newly created page in storage.
     */
    public static function store()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $data = static::get_post_data([
            'title' => 'title',
            'slug' => 'slug',
            'content' => 'content',
            'meta_description' => 'meta_description',
            'is_published' => 'is_published',
            'sort_order' => 'sort_order',
        ]);

        $validation_rules = [
            'title' => 'required|string|min:3',
            'content' => 'required|string|min:10',
            'meta_description' => 'string'
        ];

        // Default to not published if not set
        $data['is_published'] = $data['is_published'] ?? 0;

        $is_validated = static::validate_data($data, $validation_rules);

        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $page = CustomPage::create($data);

        return static::response_success([
            'message' => 'Page created successfully',
            'id' => $page->id,
            'redirect' => '/admin/pages/' . $page->id
        ]);
    }

    /**
     * Display the specified page.
     */
    public static function show($id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();
        $page = CustomPage::get_by_id($id);

        if (!$page) {
            return static::redirect('/admin/pages');
        }

        return static::view('views/admin/pages/show.php', [
            'user' => $session->user,
            'page' => $page
        ]);
    }

    /**
     * Show the form for editing the specified page.
     */
    public static function edit($id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();
        $page = CustomPage::get_by_id($id);

        if (!$page) {
            return static::redirect('/admin/pages');
        }

        return static::view('views/admin/pages/edit.php', [
            'user' => $session->user,
            'page' => $page
        ]);
    }

    /**
     * Update the specified page in storage.
     */
    public static function update($id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $page = CustomPage::get_by_id($id);

        if (!$page) {
            return static::response_error(404, 'Page not found');
        }

        $data = static::get_post_data([
            'title' => 'title',
            'slug' => 'slug',
            'content' => 'content',
            'meta_description' => 'meta_description',
            'is_published' => 'is_published',
            'sort_order' => 'sort_order',
        ]);

        $validation_rules = [
            'title' => 'required|string|min:3',
            'content' => 'required|string|min:10',
            'meta_description' => 'string'
        ];

        // Default to not published if not set
        $data['is_published'] = $data['is_published'] ?? 0;

        $is_validated = static::validate_data($data, $validation_rules);

        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $page->update($data);
        $page->save();

        return static::response_success([
            'message' => 'Page updated successfully',
            'redirect' => '/admin/pages/' . $page->id
        ]);
    }

    /**
     * Remove the specified page from storage.
     */
    public static function delete($id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $page = CustomPage::get_by_id($id);

        if (!$page) {
            return static::response_error(404, 'Page not found');
        }

        $page->delete();

        return static::response_success([
            'message' => 'Page deleted successfully',
            'redirect' => '/admin/pages'
        ]);
    }

    /**
     * Sort pages reordering.
     */
    public static function updateOrder()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $pageIds = static::get_post_data(['pages' => 'pages'])['pages'] ?? [];

        if (!is_array($pageIds) || empty($pageIds)) {
            return static::response_error(400, 'Invalid page order data');
        }

        // Start transaction
        $conn = \app\models\core\Database::getInstance()->getConnection();
        $conn->begin_transaction();

        try {
            $sortOrder = 10;
            foreach ($pageIds as $pageId) {
                $page = CustomPage::get_by_id($pageId);
                if ($page) {
                    $page->update(['sort_order' => $sortOrder]);
                    $page->save();
                    $sortOrder += 10;
                }
            }
            $conn->commit();

            return static::response_success([
                'message' => 'Page order updated successfully'
            ]);
        } catch (\Exception $e) {
            $conn->rollback();
            return static::response_error(500, 'Failed to update page order: ' . $e->getMessage());
        }
    }

    /**
     * Preview a page before publishing
     */
    public static function preview($id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();
        $page = CustomPage::get_by_id($id);

        if (!$page) {
            return static::redirect('/admin/pages');
        }

        // Use an iframe approach for preview
        return static::view('views/admin/pages/preview.php', [
            'user' => $session->user,
            'page' => $page
        ]);
    }
}
