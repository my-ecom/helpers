<?php
namespace oangia\web\entities;
use oangia\helpers\iString;
use oangia\web\entities\Model;

class Product {
    function __construct($data = []) {
        if (! empty($data)) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
            if (! $this->description) {
                $this->description = 'Material: Made From Cotton<br />Color: Printed With Different Colors<br />-Size: Various Size (From S to 5XL)<br />Style: Hoodies, Tank Tops, Youth Tees, Long Sleeve Tees, Sweatshirts, Unisex V-neck, T-shirts, and more.<br />Discount: Sale Up To 30% Off<br />Imported: From the United States';
            }
            $this->categories = json_decode($this->categories, true);
            $this->tags = json_decode($this->tags, true);
            $this->price = json_decode($this->price, true);
            $this->variations = json_decode($this->variations, true);
            $this->gallery = json_decode($this->gallery);
            if (! isset($this->price[0])) {
                $new_price = [];
                foreach ($this->price as $key => $value) {
                    $new_price[] = ['name' => $key, 'price' => $value];
                }
                $this->price = $new_price;
                $new_variations = [];
                foreach ($this->variations as $key => $variation) {
                    if ($key == 'note') {
                        $new_variation = $variation['data'][0];
                        $new_variation['type'] = 'note';
                        $new_variation['placeholder'] = 'Your name';
                    } else {
                      $new_variation = [
                          'name' => ucwords($key),
                          'slug' => $key,
                          'type' => 'select',
                          'data' => []
                      ];
                      if (isset($variation['parent'])) {
                          $new_variation['parent'] = $variation['parent'];
                      }
                      foreach ($variation['data'] as $item) {
                          $new_variation['data'][] = $item;
                      }
                    }


                    $new_variations[$key] = $new_variation;
                }
                $order = ['type', 'style', 'color', 'size', 'note'];
                $this->variations = [];
                foreach ($order as $item) {
                    if (isset($new_variations[$item])) {
                        $this->variations[] = $new_variations[$item];
                    }
                }
            }
            $this->calculatePrice();
        }
    }

    public static function list($site, $limit) {
        $sql = 'SELECT * FROM products WHERE site="' . $site . '" ORDER BY id DESC LIMIT ' . $limit;
        $result = Model::query($sql);
        $products = [];
        foreach ($result as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }

    public static function query($sql) {
        $result = Model::query($sql);
        $products = [];
        foreach ($result as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }

    public static function listByCategory($site, $slug, $limit) {
        $sql = 'SELECT * FROM products WHERE site="' . $site . '" AND categories like "%' . $slug . '%" ORDER BY id DESC LIMIT ' . $limit;
        $result = Model::query($sql);
        $products = [];
        foreach ($result as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }

    public static function listByTag($site, $slug, $limit) {
        $sql = 'SELECT * FROM products WHERE site="' . $site . '" AND tags like "%' . $slug . '%" ORDER BY id DESC LIMIT ' . $limit;
        $result = Model::query($sql);
        $products = [];
        foreach ($result as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }

    public static function search($site, $s, $limit) {
        $sql = 'SELECT * FROM products WHERE site="' . $site . '" AND title like "%' . $s . '%" ORDER BY id DESC LIMIT ' . $limit;
        $result = Model::query($sql);
        $products = [];
        foreach ($result as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }

    public static function find($site, $slug) {
        $sql = 'SELECT * FROM products WHERE slug="' . $slug . '" AND site="' . $site . '" LIMIT 1';
        $result = Model::query($sql);
        if (! $result) {
            return false;
        }
        return new Product($result[0]);
    }

    public static function related($id) {
        $limit = 5;
        $sql = 'SELECT * FROM products AS r1 JOIN (SELECT CEIL(RAND() * (SELECT MAX(id) - ' . ($limit + 1) . ' FROM products)) AS id) AS r2 WHERE r1.id >= r2.id AND r1.site = "' . $_SERVER['HTTP_HOST'] . '" ORDER BY r1.id ASC LIMIT ' . ($limit + 1) . ';';
        $result = Model::query($sql);
        $products = [];
        foreach ($result as $product) {
            if ($product['id'] != $id) {
                $products[] = new Product($product);
            }
        }
        if (count($products) == 6) {
            unset($products[5]);
        }
        return $products;
    }

    private function getCacheDir() {
        return CACHE_DIR . '/' . $_SERVER['HTTP_HOST'] . '/products/';
    }

    public function getFirebase($slug) {
        $firebase = new Model;
        return $firebase->find('sites/' . $_SERVER['HTTP_HOST'] . '/products', $slug);
    }

    public function saveCache($file_path, $data) {
        save_file($file_path, json_encode($data));
    }

    public function getCache($file_path) {
        return get_file($file_path);
    }

    public function calculatePrice() {
        $min = null;
        $max = null;
        foreach ($this->price as $price) {
            if (! $min) $min = $price['price'];
            if (! $max) $max = $price['price'];
            if ($min > $price['price']) $min = $price['price'];
            if ($max < $price['price']) $max = $price['price'];
        }

        $this->min_price = $min;
        $this->max_price = $max;
    }

    public function jsonPrice() {
        $price = [];
        foreach ($this->price as $item) {
            $price[$item['name']] = $item['price'];
        }
        return htmlentities(json_encode(array_reverse($price)));
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPermalink() {
        $slug = explode('-', $this->slug);
        $postfix = array_pop($slug);
        return site_url() . '/product/' . $postfix . '/' . implode('-', $slug);
    }

    public function getBreadcrumb() {
        $breadcrumb = [];
        $breadcrumb[] = ['Home', site_url()];
        if ($this->categories) {
            $breadcrumb[] = [trim($this->categories[0]), site_url() . '/category/' . iString::slugify($this->categories[0])];
        }
        $breadcrumb[] = [$this->title, site_url() . '/product/' . $this->slug];
        return $breadcrumb;
    }

    public function getThumbnail($type = 'thumbnail') {
        if ($this->gallery) {
            if (strpos($this->gallery[0], 'https://imgpluz.com') === 0) {
                return str_replace('https://imgpluz.com', 'https://imgpluz.com/' . $type, $this->gallery[0]);
            }
            return str_replace('https://tshirtdaily.net', 'https://imgpluz.com/' . $type, $this->gallery[0]);
        }
        return __assets('placeholder.png');
    }

    public function getThumblist() {
        $thumblist = [];
        foreach ($this->gallery as $key => $gallery) {
            if (strpos($gallery, 'https://imgpluz.com') === 0) {
                $thumblist[] = [
                    'gallery' => str_replace('https://imgpluz.com', 'https://imgpluz.com/gallery', $gallery),
                    'thumblist' => str_replace('https://imgpluz.com', 'https://imgpluz.com/glist', $gallery),
                    'origin' => $gallery
                ];
            } else {
                $thumblist[] = [
                    'gallery' => str_replace('https://tshirtdaily.net', 'https://imgpluz.com/gallery', $gallery),
                    'thumblist' => str_replace('https://tshirtdaily.net', 'https://imgpluz.com/glist', $gallery),
                    'origin' => $gallery
                ];
            }
        }

        return $thumblist;
    }

    public function priceHtml() {
        $min_price = explode('.', $this->min_price);
        if ($this->min_price == $this->max_price) {
            return '<span class="h3 fw-normal text-accent me-1">$'.$min_price[0].'.<small>'.$min_price[1].'</small></span>';
        }
        $max_price = explode('.', $this->max_price);
        return '<span class="h3 fw-normal text-accent me-1">$'.$min_price[0].'.<small>'.$min_price[1].'</small></span> - <span class="h3 fw-normal text-accent me-1">$'.$max_price[0].'.<small>'.$max_price[1].'</small></span>';
    }

    public function getTagsHtml($callback) {
        foreach ($this->tags as $tag) {
            $callback($tag, site_url().'/tag/'. iString::slugify($tag));
        }
    }

    public function getCategoriesHtml($callback) {

        foreach ($this->categories as $cat) {
            $callback($cat, site_url().'/category/'. iString::slugify($cat));
        }
    }

    private function generateOptUrl($name, $type = 'thumbnail') {
        return site_url() . '/wp-content/uploads/r1/' . $type . '/' . $name;
    }
}
