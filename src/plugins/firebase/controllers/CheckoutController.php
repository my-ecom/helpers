<?php
namespace oangia\firebase\controllers;

use oangia\firebase\Model;
use oangia\web\Request;
use oangia\web\Response;
use oangia\web\entities\Cart;

class CheckoutController {
    public function createOrder() {
        $cart = Cart::getCart();
        $data = Request::json();
        $model = new Model;
        $data['ip'] = get_user_ip();
        $data['user_agent'] = get_user_agent();
        $data['status'] = 'waiting-for-payment';
        $data['payment_type'] = 'paypal';
        $data['cart'] = $cart->cart;
        $order = $model->update('sites/' . $_SERVER['HTTP_HOST'] . '/orders', $data, __cookie('cart_session'));
        Response::json(['order_id' => __cookie('cart_session'), 'order' => $order]);
    }

    public function paymentSuccess() {
        $data = Request::json();
        $model = new Model;
        $data['status'] = 'paid';
        $model->update('sites/' . $_SERVER['HTTP_HOST'] . '/orders', $data, __cookie('cart_session'));
        unset($_COOKIE['cart_session']);
        setcookie('cart_session', null, -1, '/');
        Response::json(['transaction_id' => $data['transaction_id']]);
    }
}
