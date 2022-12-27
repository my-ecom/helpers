<?php
namespace oangia\web\controllers;
use oangia\web\entities\Post;
use oangia\web\Route;
use oangia\web\Request;
use oangia\comments\entities\Comment;

class WebController extends Controller {
    function __construct() {
        parent::__construct();
    }

    public function index() {
        $posts = Post::list(HOST, 8);
        $searchType = 'blog';
        return view('blog', compact('posts', 'searchType'));
    }

    public function single($year, $month, $slug) {
        $post = Post::find(HOST, $slug);
        $searchType = 'blog';
        $related = Post::related($post->id);
        $comments = Comment::all($post->getPermalink());
        return view('single-post', compact('post', 'searchType', 'related', 'comments'));
    }

    public function category($slug) {
        $searchType = 'blog';
        $posts = Post::listByCategory(HOST, $slug, 12);
        return view('posts/list', compact('posts', 'searchType'));
    }

    public function tag($slug) {
        $searchType = 'blog';
        $posts = Post::listByTag(HOST, $slug, 12);
        return view('posts/list', compact('posts', 'searchType'));
    }

    public function search() {
        $searchType = 'blog';
        $s = __get('s');
        $posts = Post::search(HOST, $s, 12);
        return view('posts/list', compact('posts', 'searchType'));
    }

    public function relatedPosts($id) {
        $posts = Post::related(5);
        Response::json(['data' => get_ob('view_part', ['posts/related', compact('posts')])]);
    }

    public function privacyPolicy() {
        $pageTitle = 'Privacy Policy';
        return view('pages/privacy-policy', compact('pageTitle'));
    }

    public function about() {
        $pageTitle = 'About Us';
        return view('pages/about-us', compact('pageTitle'));
    }

    public function contact() {
        $pageTitle = 'Contact Us';
        return view('pages/contact-us', compact('pageTitle'));
    }

    public function saveContact() {
        $_SESSION['message'] = 'Send request success. We\'ll respond soon';
        $data = $_POST;
        $data['ip'] = get_user_ip();
        $data['user_agent'] = get_user_agent();
        save_file(CACHE_DIR . '/contacts/' . (date('Y-m-d', time()) . '_' . time()) . '_' . $data['ip'] . '.json', json_encode($data));
        Route::redirect('/contact-us');
    }

    public function ccpa() {
        $pageTitle = 'Do not sell my personal information';
        return view('pages/ccpa', compact('pageTitle'));
    }

    public function termsOfService() {
        $pageTitle = 'Terms Of Service';
        return view('pages/terms-of-service', compact('pageTitle'));
    }

    public function secureShopping() {
        $pageTitle = 'Secure Shopping';
        return view('pages/secure-shopping', compact('pageTitle'));
    }

    public function shippingInformation() {
        $pageTitle = 'Shipping Information';
        return view('pages/shipping-information', compact('pageTitle'));
    }

    public function refundReturns() {
        $pageTitle = 'Refund and Returns Policy';
        return view('pages/refund-returns', compact('pageTitle'));
    }
}
