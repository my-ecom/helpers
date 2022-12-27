<?php
namespace oangia\web\entities;

use oangia\helpers\iString;
use oangia\web\database\Database;

class Post {
    function __construct($data = []) {
        if (! empty($data)) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
            $this->description = substr($this->content, 0, 125);
            $this->categories = json_decode($this->categories, true);
            $this->tags = json_decode($this->tags, true);
        }
    }

    public static function list($site, $limit) {
        $sql = 'SELECT * FROM posts WHERE site="' . $site . '" ORDER BY id DESC LIMIT ' . $limit;
        $result = Model::query($sql);
        $posts = [];
        foreach ($result as $post) {
            $posts[] = new Post($post);
        }
        return $posts;
    }

    public static function related($id) {
        $limit = 3;
        $sql = 'SELECT * FROM posts AS r1 JOIN (SELECT CEIL(RAND() * (SELECT MAX(id) - ' . ($limit + 1) . ' FROM posts)) AS id) AS r2 WHERE r1.id >= r2.id AND r1.site = "' . $_SERVER['HTTP_HOST'] . '" ORDER BY r1.id ASC LIMIT ' . ($limit + 1) . ';';
        $result = Model::query($sql);
        $posts = [];
        foreach ($result as $post) {
            if ($post['id'] != $id) {
                $posts[] = new Post($post);
            }
        }
        if (count($posts) == 6) {
            unset($posts[5]);
        }
        return $posts;
    }

    public static function listByCategory($site, $slug, $limit) {
        $sql = 'SELECT * FROM posts WHERE site="' . $site . '" AND categories like "%' . $slug . '%" ORDER BY id DESC LIMIT ' . $limit;
        $result = Model::query($sql);
        $posts = [];
        foreach ($result as $post) {
            $posts[] = new Post($post);
        }
        return $posts;
    }

    public static function listByTag($site, $slug, $limit) {
        $sql = 'SELECT * FROM posts WHERE site="' . $site . '" AND tags like "%' . $slug . '%" ORDER BY id DESC LIMIT ' . $limit;
        $result = Model::query($sql);
        $posts = [];
        foreach ($result as $post) {
            $posts[] = new Post($post);
        }
        return $posts;
    }

    public static function search($site, $s, $limit) {
        $sql = 'SELECT * FROM posts WHERE site="' . $site . '" AND title like "%' . $s . '%" ORDER BY id DESC LIMIT ' . $limit;
        $result = Model::query($sql);
        $posts = [];
        foreach ($result as $post) {
            $posts[] = new Post($post);
        }
        return $posts;
    }

    public static function find($site, $slug) {
        $sql = 'SELECT * FROM posts WHERE slug="' . $slug . '" AND site="' . $site . '" LIMIT 1';
        $result = Model::query($sql);
        if (! $result) {
            return false;
        }
        return new Post($result[0]);
    }

    public function getThumbnail() {
        return $this->thumbnail;
    }

    public function formatDate($field, $format = 'M d') {
        return date($format, strtotime($this->{$field}));
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getContent() {
        return $this->content;
    }

    public function getFirebase($slug) {
        $firebase = new Model;
        return $firebase->find('sites/' . $_SERVER['HTTP_HOST'] . '/posts', $slug);
    }

    public function saveCache($file_path, $data) {
        save_file($file_path, json_encode($data));
    }

    public function getCache($file_path) {
        return get_file($file_path);
    }

    private function getCacheDir() {
        return CACHE_DIR . '/' . $_SERVER['HTTP_HOST'] . '/posts/';
    }

    public static function findBySlug($slug, $cache = true) {
        if (! $cache) {
            return Post::find($slug, 'slug');
        }
        return Post::fromCache($slug);
    }

    public function getPermalink() {
        return site_url() . date('/Y/m/', strtotime($this->created_at)) . $this->slug;
    }

    public function getBreadcrumb() {
        $breadcrumb = [];
        $breadcrumb[] = ['Home', site_url()];
        $breadcrumb[] = [trim($this->categories[0]), site_url() . '/category/' . iString::slugify($this->categories[0])];
        $breadcrumb[] = [$this->title, site_url() . '/product/' . $this->slug];
        return $breadcrumb;
    }

    public function getTagsHtml($callback) {
        foreach ($this->tags as $tag) {
            $callback($tag, site_url().'/product-tag/'. iString::slugify($tag));
        }
    }

    public function getCategoriesHtml($callback) {
        foreach ($this->categories as $cat) {
            $callback($cat, site_url().'/product-category/'. iString::slugify($cat));
        }
    }

    private function generateOptUrl($name, $type = 'thumbnail') {
        return site_url() . '/wp-content/uploads/r1/' . $type . '/' . $name;
    }
}
