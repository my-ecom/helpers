<?php
namespace oangia\web\controllers;

use oangia\web\Route;
use oangia\web\Request;
use oangia\web\Response;
use oangia\web\database\Migrations;
use oangia\web\database\Database;
use oangia\database\MySQL;

class AdminController extends Controller {
    function __construct() {
        parent::__construct();
        debug_setting();
        // require auth here
        if (! isset($_COOKIE['user']) || $_COOKIE['user'] != '8921256662141a1d1cae4fc197c83e89') {
            if (! in_array(end(Route::$uri), ['login', 'auth'])) {
                Route::redirect('/sus/admin/login');
            }
        }
    }

    public function login() {
        if (isset($_COOKIE['user']) && $_COOKIE['user'] == '8921256662141a1d1cae4fc197c83e89') {
            Route::redirect('/sus/admin');
        }
    ?>
      <form action="" method="POST">
          <input type="password" name="token"/>
      </form>
    <?php
    }

    public function auth() {
        if (! isset($_POST['token'])) return Response::json(['message' => 'Unauthorization']);
        if (md5($_POST['token']) == '8921256662141a1d1cae4fc197c83e89') {
            setcookie('user', md5($_POST['token']), time() + 60 * 60 * 24 * 30, '/');
        }
        Route::redirect('/sus/admin');
    }

    public function index() {
        $contacts = array_diff(scandir(CACHE_DIR . '/contacts/'), array('.', '..'));
        $orders = MySQL::query('SELECT * FROM carts ORDER BY id DESC');
        return admin_view('admin/index', compact('contacts', 'orders'));
    }

    public function create_product_fb() {
        return admin_view('admin/crud/create-fb');
    }

    public function migrations() {
        $migration = new Migrations;
        $migration->createPosts();
        $migration->createProducts();
        $migration->createComments();
        $migration->createCarts();
        $migration->addIndex();
        return 'Done';
    }

    public function productsImages() {
        $images = MySQL::query('SELECT gallery FROM products ORDER BY id DESC');
        $result = [];
        foreach ($images as $image) {
            $image = json_decode($image['gallery'], true);
            $result = array_merge($result, $image);
        }
        echo json_encode($result);
        die();
        return $images;
    }

    public function seedProduct() {
        $data = $this->getProduct();
        $keys = [];
        $values = [];
        foreach ($data[0] as $key => $value) {
            $keys[] = $key;
        }
        for ($i = 0; $i < 8; ++$i) {
            $data = $this->getProduct();
            $value = [];
            foreach ($data[0] as $key => $_value) {
                if (is_numeric($_value)) {
                    $value[] = $_value;
                } elseif (is_array($_value)) {
                    $value[] = '"' . str_replace('"', '\"', json_encode($_value)). '"';
                } else {
                    $value[] = '"' . str_replace('"', '\"', $_value) . '"';
                }
            }
            $values[] = '(' . implode(',', $value) . ')';
        }
        #dd('INSERT INTO products (' . implode(',', $keys). ') VALUES ' . implode(',', $values) . ';');
        Database::query('INSERT INTO products (' . implode(',', $keys). ') VALUES ' . implode(',', $values) . ';');
    }

    public function seed() {
        $data = $this->getPost();
        $keys = [];
        $values = [];
        foreach ($data as $key => $value) {
            $keys[] = $key;
        }
        for ($i = 0; $i < 8; ++$i) {
            $data = $this->getPost();
            $value = [];
            foreach ($data as $key => $_value) {
                if (is_numeric($_value)) {
                    $value[] = $_value;
                } else {
                    $value[] = '"' . str_replace('"', '\"', $_value) . '"';
                }
            }
            $values[] = '(' . implode(',', $value) . ')';
        }
        Database::query('INSERT INTO posts (' . implode(',', $keys). ') VALUES ' . implode(',', $values) . ';');
    }

    private function getPost() {
        return [
            'title' => 'Alessandro Michele\'s Best Gucci Looks in Street Style',
            'slug' => slugify('Alessandro Michele\'s Best Gucci Looks in Street Style') . '_' . random_string(8),
            'content' => '*All products featured on Vogue are independently selected by our editors. However, we may earn affiliate revenue on this article and commission when you buy something.*<br/>The countdown to Black Friday is over-the day everyone has been waiting for is here with access to standout sales in full swing. It comes as no surprise that the Saks Fifth Avenue Black Friday Sale includes a range of delightful deals from classic wardrobe essentials, like easy denim, to more statement pieces, like shearling jackets and leather midi skirts. Whether you\'re looking to start your holiday shopping early or to shower yourself with fabulous fashion finds , the Saks Fifth Avenue Black Friday Sale delivers each and every time. And luckily, we\'ve combed through the racks, so that you can score the best deals the retailer has to offer-before they sell out, that is.',
            'thumbnail' => 'https://tshirtdaily.net/wp-content/uploads/News/VO080118_accessories07.jpg',
            'categories' => '["Fashion", "News", "Trens"]',
            'tags' => '["_sensitivecontent", "_syndication_noshow", "black-friday", "cyber-monday"]'
        ];
    }

    private function getProduct() {
        $slug = slugify("Fahsion 2022") . '-' . random_string(8);
        $product = '[{
	"title": "Fahsion 2022",
	"gallery": [
		"https://ae01.alicdn.com/kf/Ha275f9d846e34384aeb0bd4259ffe256a/2022-New-Fashion-Women-s-Short-Wallet-Ladies-Small-Card-Holder-Print-Lychee-Pattern-Cute-Yellow.jpg",
		"https://ae01.alicdn.com/kf/Ha233468f305c4033bcb6856d87f026edq/2022-New-Fashion-Women-s-Short-Wallet-Ladies-Small-Card-Holder-Print-Lychee-Pattern-Cute-Yellow.jpg"
	],
	"description": "description",
	"short_description": "short",
	"variations": [
		{
			"name": "Type",
			"slug": "type",
			"type": "select",
			"data": [{
					"name": "Men",
					"slug": "men"
				},
				{
					"name": "Women",
					"slug": "women"
				},
				{
					"name": "Youth",
					"slug": "youth"
				}
			]
		},
		{
			"name": "Style",
			"slug": "style",
			"type": "select",
			"parent": "type",
			"data": [{
					"name": "Classic T-Shirt",
					"slug": "classic-t-shirt",
					"image": "https://alldaytee.com/wp-content/uploads/2022/08/Classic-T-Shirt-100x100-1-100x100.png",
					"parent": ["men"]
				},
				{
					"name": "Pullover Hoodie",
					"slug": "pullover-hoodie",
					"image": "https://alldaytee.com/wp-content/uploads/2022/08/Basic-Sweatshirt-100x100-1-100x100.png",
					"parent": ["men"]
				},
				{
					"name": "Women T-Shirt",
					"slug": "women-t-shirt",
					"image": "https://alldaytee.com/wp-content/uploads/2022/08/Ladies-T-Shirt-100x100-1-100x100.png",
					"parent": ["women"]
				},
				{
					"name": "Pullover Hoodie",
					"slug": "pullover-hoodie-2",
					"image": "https://alldaytee.com/wp-content/uploads/2022/08/Women-Basic-Sweatshirt-100x100-1-100x100.png",
					"parent": ["women"]
				},
				{
					"name": "Youth T-Shirt",
					"slug": "youth-t-shirt",
					"image": "https://alldaytee.com/wp-content/uploads/2022/08/Kid-T-Shirt-100x100-1-100x100.png",
					"parent": ["youth"]
				},
				{
					"name": "Baby Onesie",
					"slug": "baby-onesie",
					"image": "https://alldaytee.com/wp-content/uploads/2022/08/Baby-Onesie-100x100-1-100x100.png",
					"parent": ["youth"]
				}
			]
		},
		{
			"name": "Color",
			"slug": "color",
			"type": "select",
			"parent": "style",
			"data": [{
					"name": "Black",
					"slug": "black",
					"hex": "000000",
					"parent": [
						"classic-t-shirt",
						"pullover-hoodie",
						"women-t-shirt",
						"pullover-hoodie-2",
						"youth-t-shirt",
						"baby-onesie"
					]
				},
				{
					"name": "Navy",
					"slug": "navy",
					"hex": "5c4881",
					"parent": [
						"classic-t-shirt",
						"women-t-shirt"
					]
				},
				{
					"name": "Red",
					"slug": "red",
					"hex": "cd0a10",
					"parent": [
						"classic-t-shirt",
						"pullover-hoodie"
					]
				}
			]
		},
		{
			"name": "Size",
			"slug": "size",
			"type": "select",
			"parent": "style",
			"data": [{
					"name": "S",
					"slug": "s",
					"parent": [
						"classic-t-shirt",
						"pullover-hoodie",
						"women-t-shirt",
						"pullover-hoodie-2",
						"youth-t-shirt",
						"baby-onesie"
					]
				},
				{
					"name": "M",
					"slug": "m",
					"parent": [
						"classic-t-shirt",
						"pullover-hoodie",
						"women-t-shirt",
						"pullover-hoodie-2",
						"youth-t-shirt",
						"baby-onesie"
					]
				},
				{
					"name": "L",
					"slug": "l",
					"parent": [
						"classic-t-shirt",
						"pullover-hoodie",
						"women-t-shirt",
						"pullover-hoodie-2"
					]
				}
			]
		},
	 	{
			"name": "Your name",
			"slug": "your-name",
			"type": "note",
			"placeholder": "Your name here",
			"tooltip": "Put your name here",
			"required": true
		}
	],
	"price": [
		{"name": "all#all#all#s", "price": "1.00"},
		{"name": "all#all#all#m", "price": "2.00"},
		{"name": "all#all#all#l", "price": "3.00"}
	],
	"categories": ["1", "2", "3"],
	"tags": ["1", "2", "3"]
}]';
        $data = json_decode($product, true);
        $data[0]['slug'] = $slug;
        return $data;
    }

    private function readAllViews($path) {
        $result = '';
        foreach (scandir($path) as $file) {
            if ($file == '.' || $file == '..') continue;
            if (is_file($path . '/' . $file)) {
                echo $path . '/' . $file . '<br/>';
                $result .= file_get_contents($path . '/' . $file);
            }
            if (is_dir($path . '/' . $file)) {
                echo $path . '/' . $file . '<br/>';
                $result .= $this->readAllViews($path . '/' . $file);
            }
        }
        return $result;
    }

    public function optimizeCss() {
        $fullHtml = new \oangia\plugins\Html;
        $mainDom = $fullHtml->parseFromUrl('http://myecom.test/usefull.html');
        #dd($mainDom);
        $css = $fullHtml->getCss($mainDom);
        unset($fullHtml);
        list($cssUse, $cssUnuse) = $css->filterUnuseCss($mainDom);
        #dd([$cssUse, $cssUnuse]);
        dd([$cssUse, $cssUnuse], false);
        unset($cssUnuse);
        $css = new \oangia\plugins\css\Css(implode('', $cssUse), true);
        $mainDom->removeUncritical('a href="https://tshirtdaily.net/tag/men-tshirt" rel="tag"');
        list($cssCritical, $cssUncritical) = $css->filterCriticalCss($mainDom);

        file_put_contents('assets/css/main.css', implode('', $cssCritical));
        file_put_contents('assets/css/addition.css', implode('', $cssUncritical));
        dd([$cssCritical, $cssUncritical]);
    }

    private function createCacheDir() {
        exec('mkdir ' . APP_PATH . '/public/cache');
        exec('sudo chmod -R 777 ' . APP_PATH . '/public/cache');
    }

    private function generateTemplateCache($file) {
        $template = file_get_contents($file);
        preg_match_all('/\{{(.*?)\}}/', $template, $matches);
        $options = include(APP_PATH . '/public/cache/' . $_SERVER['HTTP_HOST'] . '_options.php');
        foreach ($matches[0] as $key => $value) {
            if ($value == '{{content}}') continue;
            $option = isset($options[$matches[1][$key]])?$options[$matches[1][$key]]:'';
            $template = str_replace($value, $option, $template);
        }
        save_file(APP_PATH . '/public/cache/templates/' . $_SERVER['HTTP_HOST'] . '_main.php', $template);
    }

    public function build() {
        $this->createCacheDir();
        $this->generateTemplateCache(APP_PATH . '/views/templates/main.php');
        if (! isset($_GET['ref'])) {
            return 'Build done';
        }
        Route::redirect($_GET['ref']);
    }

    public function editOptions() {
        $this->createCacheDir();
        if (! file_exists(APP_PATH . '/public/cache/' . $_SERVER['HTTP_HOST'] . '_options.php')) {
            file_put_contents(APP_PATH . '/public/cache/' . $_SERVER['HTTP_HOST'] . '_options.php', file_get_contents(dirname(__DIR__) . '/options.php'));
        }
    ?>
        <style>
        textarea {width:100%}
        input {width: 100%;height:50px;margin-top:10px}
        </style>
        <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
          <textarea rows="40" name="content"><?=file_get_contents(APP_PATH . '/public/cache/' . $_SERVER['HTTP_HOST'] . '_options.php');?></textarea>
          <input type="submit" value="Save"/>
        </form>
    <?php
    }

    public function updateOptions() {
        $content = Request::post('content');
        file_put_contents(APP_PATH . '/public/cache/' . $_SERVER['HTTP_HOST'] . '_options.php', $content);
        Route::redirect('/sus/admin/build?ref=/sus/admin/edit/options');
    }
}
