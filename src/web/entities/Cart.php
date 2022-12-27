<?php
namespace oangia\web\entities;

use oangia\web\entities\Model;

class Cart {
    public $cart_contents = [];
    public $cart_session = '';
    public $content_count = 0;
    public $subtotal = '0.00';
    public $created_at = null;

    private function __construct($cart_session = '') {
        if (! $cart_session) $cart_session = __cookie('cart_session');
        if (! $cart_session) {
            $cart_session = Cart::generateCartSessionKey();
            setcookie('cart_session', $cart_session, time() + 60*60*24, '/');
        }
        $this->cart_session = $cart_session;
        $this->cart_file = CACHE_DIR . '/cart/' . $cart_session . '.json';
        $this->cart = json_decode(get_file($this->cart_file), true);

        if (! $this->cart) return;

        $this->cart_contents = $this->cart['cart_contents'];
        $this->created_at = $this->cart['created_at'];
        $this->updated_at = $this->cart['updated_at'];
        $this->ip = $this->cart['ip'];
        $this->user_agent = $this->cart['user_agent'];

        foreach ($this->cart_contents as $cart_item_key => $cart_item) {
            $this->content_count += $cart_item['quantity'];
            $this->subtotal += $cart_item['price'] * $cart_item['quantity'];
        }
    }

    public static function getCart($cart_session = '') {
        return new Cart($cart_session);
    }

    public static function addToCart($data) {
        $cart = Cart::getCart();

        $cart_item_key = Cart::generateCartItemKey($data);
        if (isset($cart->cart_contents[$cart_item_key])) {
            $cart->cart_contents[$cart_item_key]['quantity'] += $data['quantity'];
        } else {
            $cart->cart_contents[$cart_item_key] = $data;
        }

        $cart->save();
        return $cart->cart_session;
    }

    public static function updateToCart($data) {
        $cart = Cart::getCart();

        $cart_item_key = $data["cart_item_key"];
        $quantity = $data['quantity'];
        $cart->cart_contents[$cart_item_key]['quantity'] = $quantity;
        if ($quantity <= 0) {
            unset($cart->cart_contents[$cart_item_key]);
        }

        $cart->save();
        return $cart->cart_session;
    }

    public static function removeFromCart($data) {
        $cart = Cart::getCart();

        $cart_item_key = $data["cart_item_key"];
        unset($cart->cart_contents[$cart_item_key]);

        $cart->save();
        return $cart->cart_session;
    }

    public function save() {
        if (! $this->created_at) {
            $this->created_at = time();
        }
        save_file($this->cart_file, [json_encode(['cart_contents' => $this->cart_contents, 'ip' => get_user_ip(), 'user_agent' => get_user_agent(), 'created_at' => $this->created_at, 'updated_at' => time()])]);
    }

    public static function replace($data) {
        $keys = [];
        $values = [];
        foreach ($data as $key => $value) {
            $keys[] = $key;
            if (is_numeric($value)) {
                $values[] = $value;
            } elseif (is_array($value)) {
                $values[] = '"' . str_replace('"', '\"', json_encode($value)) . '"';
            } else {
                $values[] = '"' . str_replace('"', '\"', $value) . '"';
            }
        }
        $sql = 'REPLACE INTO carts (' . implode(',', $keys) . ') VALUES ('. implode(',', $values) . ')';
        return Model::query($sql);
    }

    public static function update($data) {
        $values = [];
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $values[] = $key . '=' . $value;
            } elseif (is_array($value)) {
                $values[] = $key . '=' . '"' . str_replace('"', '\"', json_encode($value)) . '"';
            } else {
                $values[] = $key . '=' . '"' . str_replace('"', '\"', $value) . '"';
            }
        }
        $sql = 'UPDATE carts SET '. implode(',', $values) . ' WHERE session_key="' . $data['session_key'] .'"';
        #dd($sql);
        return Model::query($sql);
    }

    public static function generateCartSessionKey() {
        return 't_' . md5($_SERVER['HTTP_HOST'] . '_' . get_user_ip() . '_' . microtime());
    }

    public static function generateCartItemKey($data) {
        $cart_key = '|';
        foreach ($data as $key => $value) {
            if ($key == 'price' || $key == 'quantity') continue;
            $cart_key .= $key .'=' .$value . '|';
        }
        $cart_key = md5($cart_key);
        return $cart_key;
    }
}
