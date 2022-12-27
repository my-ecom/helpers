<?php
namespace oangia\web\controllers;

use oangia\web\entities\Model;
use oangia\web\Route;
use oangia\web\entities\Product;
use oangia\web\entities\Post;
use oangia\comments\entities\Comment;
use oangia\web\Response;

class MyEComController extends Controller {
    public function product($postfix, $slug) {
        $product = Product::find(HOST, $slug . '-' . $postfix);
        if (! $product) {
            return view('404');
        }
        $comments = Comment::all($product->getPermalink());
        $product->reviewCount = count($comments);
        $product->ratingCount = 0;
        $product->avgRating = 0;
        $product->recommendRatings = 0;
        $product->rating_1 = 0;
        $product->rating_2 = 0;
        $product->rating_3 = 0;
        $product->rating_4 = 0;
        $product->rating_5 = 0;
        foreach ($comments as $comment) {
            if ($comment->rating > 0) {
                ++ $product->ratingCount;
                ++ $product->{'rating_' . $comment->rating};
                $product->avgRating += $comment->rating;
            }
            if ($comment->rating >= 4) {
                ++ $product->recommendRatings;
            }
        }
        if ($product->ratingCount > 0) {
            $product->avgRating = round($product->avgRating/$product->ratingCount, 2);
        }

        return view('product', compact('product', 'comments'));
    }

    public function home() {
        $products = Product::list(HOST, 8);
        $posts = Post::list(HOST, 3);
        return view('front-page', compact('products', 'posts'));
    }

    public function shop() {
        $products = Product::list(HOST, 12);
        return view('products/list', compact('products'));
    }

    public function category($slug) {
        $products = Product::listByCategory(HOST, $slug, 12);
        return view('products/list', compact('products'));
    }

    public function tag($slug) {
        $products = Product::listByTag(HOST, $slug, 12);
        return view('products/list', compact('products'));
    }

    public function search() {
        $s = __get('s');
        $products = Product::search(HOST, $s, 12);
        return view('products/list', compact('products'));
    }

    public function relatedProducts($id) {
        $products = Product::related($id);
        Response::json(['data' => get_ob('view_part', ['products/related', compact('products')])]);
    }

    public function oldProduct($slug) {
        $slug = explode('-', $slug);
        $postfix = array_pop($slug);
        Route::redirect(site_url() . '/product/' . $postfix . '/' . implode('-', $slug));
    }
}
