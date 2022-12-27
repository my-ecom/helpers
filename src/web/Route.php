<?php
namespace oangia\web;

use oangia\web\controllers\MyEComController;
use oangia\web\controllers\WebController;
use oangia\web\controllers\ApiController;
use oangia\web\controllers\AdminController;
use oangia\web\controllers\CrudController;
use oangia\web\controllers\CartController;
use oangia\web\controllers\CheckoutController;
use oangia\comments\controllers\CommentController;
use oangia\web\controllers\SitemapController;
use oangia\routes\BaseRoute;

class Route extends BaseRoute {
    public static function sitemap() {
        Route::get('/robots.txt',               [SitemapController::class, 'robots']);
        Route::group(['namespace' => '/seo/sitemaps'], function () {
            Route::get('/main-index.xml',       [SitemapController::class, 'index']);
            Route::get('/pages.xml',            [SitemapController::class, 'pages']);
            Route::get('/products.xml',         [SitemapController::class, 'products']);
            Route::get('/news.xml',             [SitemapController::class, 'news']);
            Route::get('/categories.xml',       [SitemapController::class, 'categories']);
            Route::get('/tags.xml',             [SitemapController::class, 'tags']);
            Route::get('/news-categories.xml',  [SitemapController::class, 'postCategories']);
            Route::get('/news-tags.xml',        [SitemapController::class, 'postTags']);
        });

    }

    public static function sus() {
        Route::group(['namespace' => '/sus'], function() {
            Route::group(['namespace' => '/api/v1', 'type' => 'api'], function () {
                Route::post('/posts',                       [ApiController::class, 'createPost']);
                Route::post('/products',                    [ApiController::class, 'createProduct']);
                Route::get('/{table}/{id}',                 [ApiController::class, 'show']);
                Route::get('/{table}',                      [ApiController::class, 'index']);
                Route::post('/{table}',                     [ApiController::class, 'store']);
                Route::put('/{table}/{id}',                 [ApiController::class, 'update']);
                Route::delete('/{table}/{id}',              [ApiController::class, 'destroy']);
            });
            Route::group(['namespace' => '/admin'], function() {
                Route::get('/',                             [AdminController::class, 'index']);
                Route::get('/migrations',                   function() {
                    $migration = new \oangia\web\database\Migrations();
                    return $migration->createOptions();
                });
                Route::get('/products/images',              [AdminController::class, 'productsImages']);
                Route::get('/build',                        [AdminController::class, 'build']);
                Route::get('/products/create',              [AdminController::class, 'create_product_fb']);
                Route::get('/css',                          [AdminController::class, 'optimizeCss']);
                Route::get('/login',                        [AdminController::class, 'login']);
                Route::post('/login',                       [AdminController::class, 'auth']);
                Route::get('/edit/options',                 [AdminController::class, 'editOptions']);
                Route::post('/edit/options',                [AdminController::class, 'updateOptions']);
                Route::get('/seed',                         [AdminController::class, 'seed']);
                Route::get('/migrations',                   [AdminController::class, 'migrations']);
                Route::get('/{table}',                      [CrudController::class, 'index']);
                Route::get('/{table}/create',               [CrudController::class, 'create']);
                Route::get('/{table}/{id}',                 [CrudController::class, 'show']);
                Route::get('/{table}/{id}/edit',            [CrudController::class, 'edit']);
            });
        });
    }

    public static function blog($home = '/') {
        Route::get($home,                                   [WebController::class, 'index']);
        Route::get('/{year}/{month}/{slug}',                [WebController::class, 'single']);
        Route::get('/post-category/{slug}',                 [WebController::class, 'category']);
        Route::get('/post-tag/{slug}',                      [WebController::class, 'tag']);
        Route::get('/post-search',                          [WebController::class, 'search']);
    }

    public static function pages() {
        Route::get('/privacy-policy',                       [WebController::class, 'privacyPolicy']);
        Route::get('/about-us',                             [WebController::class, 'about']);
        Route::get('/contact-us',                           [WebController::class, 'contact']);
        Route::post('/contact-us',                          [WebController::class, 'saveContact']);
        Route::get('/terms-of-service',                     [WebController::class, 'termsOfService']);
        Route::get('/ccpa',                                 [WebController::class, 'ccpa']);
        Route::get('/secure-shopping',                      [WebController::class, 'secureShopping']);
        Route::get('/shipping-information',                 [WebController::class, 'shippingInformation']);
        Route::get('/refund-returns',                       [WebController::class, 'refundReturns']);
    }

    public static function myecom() {
        Route::get('/',                                     [MyEComController::class, 'home']);
        Route::get('/shop',                                 [MyEComController::class, 'shop']);
        Route::get('/product/{slug}',                       [MyEComController::class, 'oldProduct']);
        Route::get('/product/{postfix}/{slug}',             [MyEComController::class, 'product']);
        Route::get('/category/{slug}',                      [MyEComController::class, 'category']);
        Route::get('/tag/{slug}',                           [MyEComController::class, 'tag']);
        Route::get('/search',                               [MyEComController::class, 'search']);
    }

    public static function api() {
        Route::group(['namespace' => '/api/v1', 'type' => 'api'], function() {
            Route::post('/comments',                        [CommentController::class, 'store']);
            Route::get('/products/related/{id}',            [MyEComController::class, 'relatedProducts']);
            Route::get('/posts/related/{id}',               [WebController::class, 'relatedPosts']);
        });
    }

    public static function cart() {
        Route::get('/cart',                                 [CartController::class, 'cart']);
        Route::get('/checkout',                             [CheckoutController::class, 'checkout']);
        Route::get('/checkout/succeed/{transaction_id}',    [CheckoutController::class, 'succeed']);
        Route::group(['namespace' => '/api/v1/checkout', 'type' => 'api'], function() {
            Route::post('/create-order',                    [CheckoutController::class, 'createOrder']);
            Route::post('/payment-success',                 [CheckoutController::class, 'paymentSuccess']);
        });
        Route::group(['namespace' => '/cart', 'type' => 'api'], function() {
            Route::post('/add-to-cart',                     [CartController::class, 'addToCart']);
            Route::post('/get-refreshed-fragments',         [CartController::class, 'getRefreshedFragments']);
            Route::post('/remove-from-cart',                [CartController::class, 'removeFromCart']);
            Route::post('/update-to-cart',                  [CartController::class, 'updateToCart']);
        });
    }
}
