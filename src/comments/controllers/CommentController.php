<?php
namespace oangia\comments\controllers;

use oangia\web\Request;
use oangia\web\Response;
use oangia\comments\entities\Comment;

class CommentController {
    public function store() {
        Request::validate([
            'content' => 'required|min:50|max:500',
            'url' => 'required',
            'author' => 'required',
            'email' => 'required|email'
        ]);
        $data = Request::json();
        $data['ip'] = get_user_ip();
        $data['post_id'] = md5('myecom_' . $data['url']);
        unset($data['url']);
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $result = Comment::create('comments', $data);
        setcookie('author', $data['author'], time() + 60 * 60 * 24 * 10, '/');
        setcookie('email', $data['email'], time() + 60 * 60 * 24 * 10, '/');
        if (isset($result['error'])) {
            Response::json(['error' => 'Some errors'], 400);
        }
        return $result;
    }
}
