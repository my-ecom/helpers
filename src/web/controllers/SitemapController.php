<?php
namespace oangia\web\controllers;

use oangia\web\Response;
use oangia\database\MySQL;

class SitemapController extends Controller {
    function __construct() {
        parent::__construct();
    }
    public function index() {
        $site_url = site_url();
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $sitemap .= '<sitemap><loc>' . $site_url . '/seo/sitemaps/products.xml</loc><lastmod>2022-12-24</lastmod></sitemap>'. PHP_EOL;
        $sitemap .= '<sitemap><loc>' . $site_url . '/seo/sitemaps/categories.xml</loc><lastmod>2022-12-24</lastmod></sitemap>' . PHP_EOL;
        $sitemap .= '<sitemap><loc>' . $site_url . '/seo/sitemaps/tags.xml</loc><lastmod>2022-12-24</lastmod></sitemap>' . PHP_EOL;
        $sitemap .= '<sitemap><loc>' . $site_url . '/seo/sitemaps/news.xml</loc><lastmod>2022-12-24</lastmod></sitemap>' . PHP_EOL;
        $sitemap .= '<sitemap><loc>' . $site_url . '/seo/sitemaps/news-categories.xml</loc><lastmod>2022-12-24</lastmod></sitemap>' . PHP_EOL;
        $sitemap .= '<sitemap><loc>' . $site_url . '/seo/sitemaps/news-tags.xml</loc><lastmod>2022-12-24</lastmod></sitemap>' . PHP_EOL;
        $sitemap .= '<sitemap><loc>' . $site_url . '/seo/sitemaps/pages.xml</loc><lastmod>2022-12-24</lastmod></sitemap>' . PHP_EOL;
        $sitemap .= '</sitemapindex>';
        Response::xml($sitemap);
    }

    public function products() {
        $sql = 'SELECT slug FROM products WHERE site="' . HOST . '" ORDER BY id DESC';
        $products = MySQL::query($sql);
        $site_url = site_url();
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($products as $product) {
            $slug = explode('-', $product['slug']);
            $postfix = array_pop($slug);
            $sitemap .= '<url><loc>' . $site_url . '/product/' . $postfix . '/' . implode('-', $slug) . '</loc></url>';
        }
        $sitemap .= '</urlset>';
        Response::xml($sitemap);
    }

    public function news() {
        $sql = 'SELECT slug, created_at FROM posts WHERE site="' . HOST . '" ORDER BY id DESC';
        $posts = MySQL::query($sql);
        $site_url = site_url();
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($posts as $post) {
            $postfix = date('Y/m', strtotime($post['created_at']));
            $sitemap .= '<url><loc>' . $site_url . '/' . $postfix . '/' . $post['slug'] . '</loc></url>';
        }
        $sitemap .= '</urlset>';
        Response::xml($sitemap);
    }

    public function categories() {
        $sql = 'SELECT categories FROM products WHERE site="' . HOST . '" ORDER BY id DESC';
        $categories = MySQL::query($sql);
        $uniqueCategories = [];
        foreach ($categories as $category) {
            $uniqueCategories = array_merge($uniqueCategories, json_decode($category['categories'], true));
        }
        $uniqueCategories = array_unique($uniqueCategories);
        $site_url = site_url();
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($uniqueCategories as $category) {
            $sitemap .= '<url><loc>' . $site_url . '/category/' . slugify($category) . '</loc></url>';
        }
        $sitemap .= '</urlset>';
        Response::xml($sitemap);
    }

    public function tags() {
        $sql = 'SELECT tags FROM products WHERE site="' . HOST . '" ORDER BY id DESC';
        $tags = MySQL::query($sql);
        $uniqueTags = [];
        foreach ($tags as $tag) {
            $uniqueTags = array_merge($uniqueTags, json_decode($tag['tags'], true));
        }
        $uniqueTags = array_unique($uniqueTags);
        $site_url = site_url();
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($uniqueTags as $tag) {
            $sitemap .= '<url><loc>' . $site_url . '/tag/' . slugify($tag) . '</loc></url>';
        }
        $sitemap .= '</urlset>';
        Response::xml($sitemap);
    }

    public function postCategories() {
        $sql = 'SELECT categories FROM posts WHERE site="' . HOST . '" ORDER BY id DESC';
        $categories = MySQL::query($sql);
        $uniqueCategories = [];
        foreach ($categories as $category) {
            $uniqueCategories = array_merge($uniqueCategories, json_decode($category['categories'], true));
        }
        $uniqueCategories = array_unique($uniqueCategories);
        $site_url = site_url();
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($uniqueCategories as $category) {
            $sitemap .= '<url><loc>' . $site_url . '/post-category/' . slugify($category) . '</loc></url>';
        }
        $sitemap .= '</urlset>';
        Response::xml($sitemap);
    }

    public function postTags() {
        $sql = 'SELECT tags FROM posts WHERE site="' . HOST . '" ORDER BY id DESC';
        $tags = MySQL::query($sql);
        $uniqueTags = [];
        foreach ($tags as $tag) {
            $uniqueTags = array_merge($uniqueTags, json_decode($tag['tags'], true));
        }
        $uniqueTags = array_unique($uniqueTags);
        $site_url = site_url();
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($uniqueTags as $tag) {
            $sitemap .= '<url><loc>' . $site_url . '/post-tag/' . slugify($tag) . '</loc></url>';
        }
        $sitemap .= '</urlset>';
        Response::xml($sitemap);
    }

    public function pages() {
        $site_url = site_url();
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $sitemap .= '<url><loc>' . $site_url . '</loc></url>';
        $sitemap .= '<url><loc>' . $site_url . '/blog</loc></url>';
        $sitemap .= '<url><loc>' . $site_url . '/shop</loc></url>';
        $sitemap .= '<url><loc>' . $site_url . '/contact-us</loc></url>';
        $sitemap .= '<url><loc>' . $site_url . '/about-us</loc></url>';
        $sitemap .= '<url><loc>' . $site_url . '/shipping-information</loc></url>';
        $sitemap .= '<url><loc>' . $site_url . '/refund-returns</loc></url>';
        $sitemap .= '<url><loc>' . $site_url . '/secure-shopping</loc></url>';
        $sitemap .= '</urlset>';
        Response::xml($sitemap);
    }

    public function robots() {
        $robots = 'User-agent: *' . PHP_EOL;
        $robots .= 'Disallow: /cart'  . PHP_EOL;
        $robots .= 'Disallow: /checkout'  . PHP_EOL  . PHP_EOL;
        $robots .= 'Sitemap: ' . site_url() . '/seo/sitemaps/main-index.xml';
        Response::txt($robots);
    }
}
