<?php
namespace oangia\firebase;

class Firebase {
    public $base_url = 'https://firestore.googleapis.com/v1/projects/';

    function __construct($config) {
        $this->config = $config;
    }

    public function urlGet($database, $collection, $id) {
        return $this->base_url . $this->config['projectId'] . '/databases/' . $database . '/documents/' . $collection . '/' . $id . '?key=' . $this->config['apiKey'];
    }

    public function urlPost($database, $collection, $id = '') {
        return $this->base_url . $this->config['projectId'] . '/databases/' . $database . '/documents/' . $collection . '?' . ($id ? 'documentId=' . $id . '&': '') . 'key=' . $this->config['apiKey'];
    }
}
