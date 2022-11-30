<?php

namespace oangia\firebase\api;
use oangia\firebase\FireStore;

class Comment {
    function __construct(FireStore $firestore) {
        $this->firestore = $firestore;
        $this->site = $_SERVER['SERVER_NAME'];
    }

    public function setSite($site) {
        $this->site = $site;
    }

    public function getComments($post_id) {
        $firestore_id = $this->generateFireStoreId($post_id);
        return $this->firestore->getDoc('comments', $firestore_id);
    }

    public function addComment($post_id, $comment) {
        $firestore_id = $this->generateFireStoreId($post_id);
        $comment['createTime'] = time();
        $comment['updateTime'] = time();
        return $this->firestore->addDoc('comments/' . $firestore_id . '/comments', $comment);
    }

    private function generateFireStoreId($post_id) {
        $site_id = substr(md5('oaniga_' . $this->site), 0, 11);
        return $site_id . '_' . $post_id;
    }
}
