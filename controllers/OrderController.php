<?php

namespace app\controllers;

use app\controllers\core\Controller;
use app\models\Order;
use app\models\OrdersAccess;

class OrderController extends Controller
{
    protected static $model = Order::class;

    public static function index()
    {
        $key = static::get_query_field('key');
        $email = static::get_query_field('email');

        if ($key && $email) {
            $validation = [
                'key' => $key,
                'email' => $email,
            ];

            $is_validated = static::validate_data($validation, [
                'email' => 'required|string|email',
                'key' => 'required|string',
            ]);

            if (!$is_validated) {
                return static::view('views/orders.php', [
                    'status' => 'error',
                    'message' => 'Неправильный формат ключа или email',
                ]);
            }

            $access = OrdersAccess::login($key, $email);
            if (!$access) {
                return static::view('views/orders.php', [
                    'status' => 'error',
                    'message' => 'Неправильный ключ или email',
                ]);
            }

            $orders = $access->orders;
            return static::view('views/orders.php', [
                'status' => 'success',
                'orders' => $orders,
                'access' => $access,
            ]);
        }

        return static::view('views/orders.php', [
            'status' => 'form'
        ]);
    }

    public static function show_order($order_id)
    {
        $key = static::get_query_field('key');
        $email = static::get_query_field('email');

        // Validate access first
        if (!$key || !$email) {
            return static::redirect('/orders');
        }

        $access = OrdersAccess::login($key, $email);
        if (!$access) {
            return static::redirect('/orders');
        }

        // Get order and confirm it belongs to this email
        $order = Order::get_by_id($order_id);
        if (!$order || $order->customer_email !== $email) {
            return static::redirect('/orders?key=' . $key . '&email=' . $email);
        }

        return static::view('views/order.php', [
            'order' => $order,
            'access' => $access
        ]);
    }

    public static function genererate_access()
    {
        $email = static::get_post_field('email');

        $validation = [
            'email' => $email
        ];

        $is_validated = static::validate_data($validation, [
            'email' => 'required|string|email'
        ]);

        if (!$is_validated) {
            return static::response_error(400, 'Неправильный формат email');
        }

        // Check if orders exist for this email
        $orders = Order::where('customer_email', '=', $email)->get();
        if (count($orders) === 0) {
            return static::response_error(400, 'Заказы не найдены для этого email');
        }

        // Create or refresh access
        $access = OrdersAccess::create([
            'email' => $email
        ]);

        if (!$access) {
            return static::response_success([
                'status' => 'error',
                'message' => 'Не удалось создать доступ. Попробуйте позже.'
            ]);
        }

        // Send email with access link (mock in this implementation)
        $sent = static::send_access_email($email, $access);

        return static::response_success([
            'status' => 'success',
            'message' => 'Ссылка для доступа отправлена на указанный email'
        ]);
    }

    private static function send_access_email($email, $access)
    {
        global $SITE_URL;

        // Email details
        $to = $email;
        $subject = "Доступ к вашим заказам";

        // Create HTML email body
        $message = "
        <html>
        <head>
            <title>Доступ к вашим заказам</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { color: #739186; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
                .content { margin-bottom: 30px; }
                .button { display: inline-block; background-color: #739186; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; }
                .footer { font-size: 12px; color: #999; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>Доступ к вашим заказам</div>
                <div class='content'>
                    <p>Здравствуйте!</p>
                    <p>Вы запросили доступ к истории ваших заказов.</p>
                    <p>Перейдите по ссылке ниже, чтобы просмотреть ваши заказы:</p>
                    <p><a href='{$access->access_url}' class='button'>Просмотреть заказы</a></p>
                    <p>Если вы не запрашивали доступ к заказам, пожалуйста, проигнорируйте это письмо.</p>
                    <p>Ссылка действительна в течение 1 часа.</p>
                </div>
                <div class='content'>
                    <p>Если кнопка выше не работает, скопируйте и вставьте следующую ссылку в адресную строку браузера:</p>
                    <p>{$access->access_url}</p>
                </div>
                <div class='footer'>
                    <p>С уважением, Команда поддержки магазина ZoVisland</p>
                    <p>© " . date('Y') . " ZoVisland</p>
                </div>
            </div>
        </body>
        </html>
        ";

        // Email headers
        $headers = array(
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: noreply@' . $_SERVER['HTTP_HOST'],
            'Reply-To: support@' . $_SERVER['HTTP_HOST'],
            'X-Mailer: PHP/' . phpversion()
        );

        // Log the email sending attempt
        error_log("Attempting to send access email to: " . $email);
        error_log("Access URL: " . $access->access_url);

        // Send email
        $sent = mail($to, $subject, $message, implode("\r\n", $headers));

        if (!$sent) {
            error_log("Failed to send access email to: " . $email);
            return false;
        }

        error_log("Successfully sent access email to: " . $email);
        return true;
    }
}
