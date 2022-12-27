<?php
namespace oangia\web\entities;

class CartItem {
    function __construct($cart_item_key, $cart_item) {
        $this->cart_item_key = $cart_item_key;
        $this->cart_item = $cart_item;
    }

    public function getTotal() {
        return $this->cart_item['quantity'] * $this->cart_item['price'];
    }
}
