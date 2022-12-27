<?php
namespace oangia\web\database;
use oangia\web\entities\Model;

class Migrations {
    function __construct() {

    }

    public function createPosts() {
        $table = 'posts';
        $fields = [
            'id' => 'bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'author' => 'bigint(20) UNSIGNED NOT NULL DEFAULT 1',
            'title' => 'varchar(256) NOT NULL',
            'slug' => 'varchar(256) UNIQUE NOT NULL',
            'thumbnail' => 'varchar(256) NOT NULL',
            'categories' => 'text',
            'tags' => 'text',
            'content' => 'text NOT NULL',
            'description' => 'varchar(256)',
            'status' => 'varchar(32) NOT NULL DEFAULT "publish"',
            'allow_comment' => 'tinyint NOT NULL DEFAULT 1',
            'comment_count' => 'int UNSIGNED NOT NULL DEFAULT 0',
            'site' => 'varchar(64) NOT NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ];
        return $this->run($table, $fields);
    }

    public function createProducts() {
        $table = 'products';
        $fields = [
            'id' => 'bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'author' => 'bigint(20) UNSIGNED NOT NULL DEFAULT 1',
            'title' => 'varchar(256) NOT NULL',
            'slug' => 'varchar(256) UNIQUE NOT NULL',
            'thumbnail' => 'varchar(256)',
            'categories' => 'text',
            'tags' => 'text',
            'description' => 'text',
            'short_description' => 'varchar(256)',
            'variations' => 'text',
            'price' => 'text',
            'min_price' => 'double(8, 2)',
            'max_price' => 'double(8, 2)',
            'sale_percent' => 'TINYINT default 100',
            'gallery' => 'text',
            'origin_gallery' => 'text',
            'status' => 'varchar(32) NOT NULL DEFAULT "publish"',
            'allow_review' => 'TINYINT DEFAULT 1',
            'allow_rating' => 'TINYINT DEFAULT 1',
            'require_rating' => 'tinyint DEFAULT 0',
            'site' => 'varchar(64) NOT NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ];
        return $this->run($table, $fields);
    }

    public function createComments() {
        $table = 'comments';
        $fields = [
            'id' => 'bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'post_id' => 'varchar(32) NOT NULL', // md5 of myecom_{{link}}
            'author_id' => 'bigint(20) UNSIGNED NOT NULL DEFAULT 0',
            'author' => 'varchar(128) NOT NULL',
            'email' => 'varchar(128) NOT NULL',
            'ip' => 'varchar(64) NOT NULL',
            'user_agent' => 'varchar(256) NOT NULL',
            'content' => 'varchar(512) NOT NULL',
            'approved' => 'tinyint(1) NOT NULL DEFAULT 1',
            'verified' => 'tinyint(1) NOT NULL DEFAULT 0',
            'rating' => 'smallint(4) NOT NULL DEFAULT 0',
            'parent_id' => 'bigint(20) UNSIGNED NOT NULL DEFAULT 0',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ];
        return $this->run($table, $fields);
    }

    public function createCarts() {
        $table = 'carts';
        $fields = [
            'id' => 'bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'user_id' => 'bigint(20) UNSIGNED NOT NULL DEFAULT 0',
            'session_key' => 'varchar(64) UNIQUE NOT NULL',
            'ip' => 'varchar(64) NOT NULL',
            'user_agent' => 'varchar(256) NOT NULL',
            'cart' => 'text NOT NULL',
            'first_name' => 'varchar(128) NOT NULL',
            'last_name' => 'varchar(128) NOT NULL',
            'company' => 'varchar(256) NOT NULL',
            'country' => 'varchar(128) NOT NULL',
            'address_1' => 'varchar(256) NOT NULL',
            'address_2' => 'varchar(256) NOT NULL',
            'postcode' => 'varchar(16) NOT NULL',
            'city' => 'varchar(256) NOT NULL',
            'phone' => 'varchar(32) NOT NULL',
            'email' => 'varchar(128) NOT NULL',
            'status' => 'varchar(32) NOT NULL default "processing"',
            'payment_status' => 'varchar(32) NOT NULL default "waiting"',
            'transaction_id' => 'varchar(128) NOT NULL default ""',
            'payment_type' => 'varchar(64) NOT NULL default ""',
            'note' => 'varchar(512) NOT NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ];
        $cart = $this->run($table, $fields);
        return [$cart];
    }

    public function createOptions() {
        $table = 'options';
        $fields = [
            'id' => 'bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name' => 'varchar(256) NOT NULL',
            'value' => 'text',
            'site' => 'varchar(256) NOT NULL',
        ];
        return $this->run($table, $fields);
    }

    public function addIndex() {
        $sql = 'CREATE INDEX comments ON comments (post_id, author_id, email, ip, parent_id);';
        Database::query($sql);
        $sql = 'CREATE INDEX carts ON carts (user_id, ip, email, transaction_id);';
        Database::query($sql);
        $sql = 'CREATE INDEX posts ON posts (site);';
        Database::query($sql);
        $sql = 'CREATE INDEX products ON products (site);';
        Database::query($sql);
    }

    private function run($table, $fields) {
        $sql = 'DROP TABLE IF EXISTS ' . $table . ';';
        Database::query($sql);
        $sql = 'CREATE TABLE ' . $table . ' (';
        foreach ($fields as $field => $type) {
            $sql .= $field . ' ' . $type . ',';
        }
        $sql = trim($sql, ',');
        $sql .= ')';
        return Database::query($sql);
    }
}
