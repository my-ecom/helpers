<?php
namespace oangia\web\controllers;

use oangia\web\Request;
use oangia\web\Response;
use oangia\web\entities\Cart;

class CheckoutController extends Controller {
    public function checkout() {
        $cart = Cart::getCart();
        $purchase_units = [];
        foreach ($cart->cart_contents as $cart_item_key => $cart_item) {
            $purchase_units[] = ['name' => $cart_item['title'], 'unit_amount' => ['value' => floatval($cart_item['price']), 'currency_code' => 'USD'], 'quantity' => intval($cart_item['quantity'])];
        }
        return view('checkout', compact('cart', 'purchase_units'));
    }

    public function succeed($transaction_id) {
        return view('checkout/succeed', compact('transaction_id'));
    }

    public function createOrder() {
        $cart = Cart::getCart();
        $data = Request::json();
        $data['ip'] = get_user_ip();
        $data['user_agent'] = get_user_agent();
        $data['status'] = 'waiting-for-payment';
        $data['payment_type'] = 'paypal';
        $data['cart'] = $cart->cart;
        $data['session_key'] = __cookie('cart_session');
        $order = Cart::replace($data);
        Response::json(['order_id' => __cookie('cart_session'), 'order' => $order]);
    }

    public function paymentSuccess() {
        $data = Request::json();
        $data['status'] = 'paid';
        $data['session_key'] = __cookie('cart_session');
        $order = Cart::update($data);
        unset($_COOKIE['cart_session']);
        setcookie('cart_session', null, -1, '/');
        Response::json(['transaction_id' => $data['transaction_id']]);
    }
}
