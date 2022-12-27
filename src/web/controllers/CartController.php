<?php
namespace oangia\web\controllers;

use oangia\web\Request;
use oangia\web\Response;
use oangia\web\entities\Cart;
use oangia\helpers\iString;
use oangia\web\Client;

class CartController extends Controller {
    public function cart() {
        $cart = Cart::getCart();
        return view('cart', compact('cart'));
    }

    public function getRefreshedFragments($cart_session = '') {
        $cart = Cart::getCart($cart_session);

        Response::json([
          'fragments' => [
            'div.widget_shopping_cart_content' => get_ob('view_part', ['cart/mini-cart', compact('cart')]),
            'a.cart-text' => get_ob('view_part', ['cart/cart-text', compact('cart')]),
            'a.cart-badge' => get_ob('view_part', ['cart/cart-content', compact('cart')]),
            '.subtotal' => get_ob('view_part', ['price', ['price' => $cart->subtotal]])
          ]
        ]);
    }

    public function addToCart() {
        $data = Request::json();
        $cart_session = Cart::addToCart($data);

        $this->getRefreshedFragments($cart_session);
    }

    public function removeFromCart() {
        $data = Request::json();
        $cart_session = Cart::removeFromCart($data);
        $this->getRefreshedFragments($cart_session);
    }

    public function updateToCart() {
        $data = Request::json();
        $cart_session = Cart::updateTocart($data);
        $this->getRefreshedFragments($cart_session);
    }
}
