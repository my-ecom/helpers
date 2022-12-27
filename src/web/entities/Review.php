<?php
namespace oangia\web\entities;

class Review extends Model {
    protected $table = 'comments';
    protected $fillable = ['id', 'author_id', 'author', 'email', 'ip', 'content', 'user_agent', 'post_id', 'approved', 'verified', 'rating', 'parent_id', 'created_at', 'updated_at'];
}
