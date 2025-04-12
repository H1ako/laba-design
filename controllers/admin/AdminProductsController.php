<?php

namespace app\controllers\admin;

use app\controllers\core\Controller;
use app\models\Product;
use app\models\ProductCharacteristic;
use app\models\ProductImage;
use app\models\ProductSize;
use app\models\Session;

class AdminProductsController extends Controller
{
    public static function index()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $session = Session::get();
        
        $search = static::get_query_field('search');
        $page = max(1, intval(static::get_query_field('page') ?? 1));
        $per_page = 20;
        
        $query = Product::order_by('created_at', 'DESC');
        
        if ($search) {
            $query = $query->where_raw("(name LIKE ? OR description LIKE ?)", ["%$search%", "%$search%"]);
        }
        
        $total_products = $query->count();
        $total_pages = ceil($total_products / $per_page);
        
        $products = $query->limit($per_page)->offset(($page - 1) * $per_page)->get();
        
        return static::view('views/admin/products/index.php', [
            'user' => $session->user,
            'products' => $products,
            'total_products' => $total_products,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'search' => $search
        ]);
    }
    
    public static function create()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $session = Session::get();
        
        return static::view('views/admin/products/create.php', [
            'user' => $session->user
        ]);
    }
    
    public static function store()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $data = static::get_post_data([
            'name' => 'name',
            'base_price' => 'base_price',
            'discount_price' => 'discount_price',
            'description' => 'description'
        ]);
        
        $is_validated = static::validate_data($data, [
            'name' => 'required|string|min:3',
            'base_price' => 'required|numeric|min:0',
            'discount_price' => 'numeric|min:0',
            'description' => 'string'
        ]);
        
        if (!$is_validated) {
            return static::response_error(400, 'Неверные данные');
        }
        
        // Handle thumb upload
        if (isset($_FILES['thumb']) && $_FILES['thumb']['error'] == 0) {
            $upload_dir = __DIR__ . '/../../assets/images/products/';
            $filename = uniqid() . '_' . basename($_FILES['thumb']['name']);
            $upload_path = $upload_dir . $filename;
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['thumb']['tmp_name'], $upload_path)) {
                $data['thumb'] = '/assets/images/products/' . $filename;
            }
        }
        
        $product = Product::create($data);
        
        return static::response_success([
            'message' => 'Товар создан успешно',
            'redirect' => '/admin/products/' . $product->id
        ]);
    }
    
    public static function show($product_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $session = Session::get();
        
        $product = Product::get_by_id($product_id);
        
        if (!$product) {
            return static::redirect('/admin/products');
        }
        
        return static::view('views/admin/products/show.php', [
            'user' => $session->user,
            'product' => $product
        ]);
    }
    
    public static function edit($product_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $session = Session::get();
        
        $product = Product::get_by_id($product_id);
        
        if (!$product) {
            return static::redirect('/admin/products');
        }
        
        return static::view('views/admin/products/edit.php', [
            'user' => $session->user,
            'product' => $product
        ]);
    }
    
    public static function update($product_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $product = Product::get_by_id($product_id);
        
        if (!$product) {
            return static::response_error(404, 'Товар не найден');
        }
        
        $data = static::get_post_data([
            'name' => 'name',
            'base_price' => 'base_price',
            'discount_price' => 'discount_price',
            'description' => 'description'
        ]);
        
        $is_validated = static::validate_data($data, [
            'name' => 'required|string|min:3',
            'base_price' => 'required|numeric|min:0',
            'discount_price' => 'numeric|min:0',
            'description' => 'string'
        ]);
        
        if (!$is_validated) {
            return static::response_error(400, 'Неверные данные');
        }
        
        // Handle thumb upload if present
        if (isset($_FILES['thumb']) && $_FILES['thumb']['error'] == 0) {
            $upload_dir = __DIR__ . '/../../assets/images/products/';
            $filename = uniqid() . '_' . basename($_FILES['thumb']['name']);
            $upload_path = $upload_dir . $filename;
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['thumb']['tmp_name'], $upload_path)) {
                $data['thumb'] = '/assets/images/products/' . $filename;
            }
        }
        
        $product->update($data);
        
        return static::response_success([
            'message' => 'Товар обновлен успешно',
            'product' => $product->to_array()
        ]);
    }
    
    public static function delete($product_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $product = Product::get_by_id($product_id);
        
        if (!$product) {
            return static::response_error(404, 'Товар не найден');
        }
        
        // Delete product images
        $images = ProductImage::where('product_id', '=', $product_id)->get();
        foreach ($images as $image) {
            $image->delete();
        }
        
        // Delete product characteristics
        $characteristics = ProductCharacteristic::where('product_id', '=', $product_id)->get();
        foreach ($characteristics as $characteristic) {
            $characteristic->delete();
        }
        
        // Delete product sizes
        $sizes = ProductSize::where('product_id', '=', $product_id)->get();
        foreach ($sizes as $size) {
            $size->delete();
        }
        
        // Delete the product
        $product->delete();
        
        return static::response_success([
            'message' => 'Товар удален успешно',
            'redirect' => '/admin/products'
        ]);
    }
    
    public static function add_characteristic($product_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $product = Product::get_by_id($product_id);
        
        if (!$product) {
            return static::response_error(404, 'Товар не найден');
        }
        
        $data = static::get_post_data([
            'name' => 'name',
            'value' => 'value'
        ]);
        
        $is_validated = static::validate_data($data, [
            'name' => 'required|string',
            'value' => 'required|string'
        ]);
        
        if (!$is_validated) {
            return static::response_error(400, 'Неверные данные');
        }
        
        $data['product_id'] = $product_id;
        $characteristic = ProductCharacteristic::create($data);
        
        return static::response_success([
            'message' => 'Характеристика добавлена',
            'characteristic' => $characteristic->to_array()
        ]);
    }
    
    public static function update_characteristic($product_id, $characteristic_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $characteristic = ProductCharacteristic::get_by_id($characteristic_id);
        
        if (!$characteristic || $characteristic->product_id != $product_id) {
            return static::response_error(404, 'Характеристика не найдена');
        }
        
        $data = static::get_post_data([
            'name' => 'name',
            'value' => 'value'
        ]);
        
        $is_validated = static::validate_data($data, [
            'name' => 'required|string',
            'value' => 'required|string'
        ]);
        
        if (!$is_validated) {
            return static::response_error(400, 'Неверные данные');
        }
        
        $characteristic->update($data);
        
        return static::response_success([
            'message' => 'Характеристика обновлена',
            'characteristic' => $characteristic->to_array()
        ]);
    }
    
    public static function remove_characteristic($product_id, $characteristic_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $characteristic = ProductCharacteristic::get_by_id($characteristic_id);
        
        if (!$characteristic || $characteristic->product_id != $product_id) {
            return static::response_error(404, 'Характеристика не найдена');
        }
        
        $characteristic->delete();
        
        return static::response_success([
            'message' => 'Характеристика удалена'
        ]);
    }
    
    public static function add_size($product_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $product = Product::get_by_id($product_id);
        
        if (!$product) {
            return static::response_error(404, 'Товар не найден');
        }
        
        $data = static::get_post_data([
            'size' => 'size',
            'in_stock' => 'in_stock'
        ]);
        
        $is_validated = static::validate_data($data, [
            'size' => 'required|string',
            'in_stock' => 'boolean'
        ]);
        
        if (!$is_validated) {
            return static::response_error(400, 'Неверные данные');
        }
        
        $data['product_id'] = $product_id;
        $size = ProductSize::create($data);
        
        return static::response_success([
            'message' => 'Размер добавлен',
            'size' => $size->to_array()
        ]);
    }
    
    public static function update_size($product_id, $size_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $size = ProductSize::get_by_id($size_id);
        
        if (!$size || $size->product_id != $product_id) {
            return static::response_error(404, 'Размер не найден');
        }
        
        $data = static::get_post_data([
            'size' => 'size',
            'in_stock' => 'in_stock'
        ]);
        
        $is_validated = static::validate_data($data, [
            'size' => 'required|string',
            'in_stock' => 'boolean'
        ]);
        
        if (!$is_validated) {
            return static::response_error(400, 'Неверные данные');
        }
        
        $size->update($data);
        
        return static::response_success([
            'message' => 'Размер обновлен',
            'size' => $size->to_array()
        ]);
    }
    
    public static function remove_size($product_id, $size_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $size = ProductSize::get_by_id($size_id);
        
        if (!$size || $size->product_id != $product_id) {
            return static::response_error(404, 'Размер не найден');
        }
        
        $size->delete();
        
        return static::response_success([
            'message' => 'Размер удален'
        ]);
    }
    
    public static function add_image($product_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $product = Product::get_by_id($product_id);
        
        if (!$product) {
            return static::response_error(404, 'Товар не найден');
        }
        
        // Get max sort order
        $max_sort = 0;
        $images = ProductImage::where('product_id', '=', $product_id)->get();
        foreach ($images as $image) {
            if ($image->sort_order > $max_sort) {
                $max_sort = $image->sort_order;
            }
        }
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = __DIR__ . '/../../assets/images/products/';
            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $upload_path = $upload_dir . $filename;
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = '/assets/images/products/' . $filename;
                
                $image = ProductImage::create([
                    'product_id' => $product_id,
                    'image_path' => $image_path,
                    'sort_order' => $max_sort + 1
                ]);
                
                return static::response_success([
                    'message' => 'Изображение добавлено',
                    'image' => $image->to_array()
                ]);
            }
        }
        
        return static::response_error(400, 'Ошибка загрузки изображения');
    }
    
    public static function update_image_sort($product_id, $image_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $image = ProductImage::get_by_id($image_id);
        
        if (!$image || $image->product_id != $product_id) {
            return static::response_error(404, 'Изображение не найдено');
        }
        
        $sort_order = static::get_post_field('sort_order');
        if ($sort_order === null) {
            return static::response_error(400, 'Необходимо указать порядок сортировки');
        }
        
        $image->update(['sort_order' => $sort_order]);
        
        return static::response_success([
            'message' => 'Порядок сортировки обновлен',
            'image' => $image->to_array()
        ]);
    }
    
    public static function remove_image($product_id, $image_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $image = ProductImage::get_by_id($image_id);
        
        if (!$image || $image->product_id != $product_id) {
            return static::response_error(404, 'Изображение не найдено');
        }
        
        $image->delete();
        
        return static::response_success([
            'message' => 'Изображение удалено'
        ]);
    }
}