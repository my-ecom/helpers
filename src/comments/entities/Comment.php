<?php
namespace oangia\comments\entities;

use oangia\mvc\models\MySQL;

class Comment extends MySQL{
    function __construct($data = []) {
        if (! empty($data)) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
    public static function all($link) {
        $sql = 'SELECT * FROM comments WHERE post_id="' . md5('myecom_' . $link) . '" ORDER BY created_at DESC';
        $result = Comment::query($sql);
        $comments = [];
        foreach ($result as $comment) {
            $comments[] = new Comment($comment);
        }
        return $comments;
    }
}
