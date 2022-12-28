<?php
namespace oangia\firebase;

class Firebase {
    public $base_url = 'https://firestore.googleapis.com/v1/projects/';

    function __construct() {}

    public function urlGet($database, $collection, $id) {
        return $this->base_url . $this->getProjectId() . '/databases/' . $database . '/documents/' . $collection . '/' . $id . '?key=' . $this->getApiKey();
    }

    public function urlPost($database, $collection, $id = '') {
        return $this->base_url . $this->getProjectId() . '/databases/' . $database . '/documents/' . $collection . '?' . ($id ? 'documentId=' . $id . '&': '') . 'key=' . $this->getApiKey();
    }

    public function urlQuery($database, $document = '') {
        return $this->base_url . $this->getProjectId() . '/databases/' . $database . '/documents' . $document . ':runQuery';
    }

    private function getProjectId() {
        if (! defined('FB_PROJECT_ID')) {
          die('Need to declare FB_PROJECT_ID to use firebase');
        }
        return FB_PROJECT_ID;
    }

    private function getApiKey() {
        if (! defined('FB_API_KEY')) {
          die('Need to declare FB_API_KEY to use firebase');
        }
        return FB_API_KEY;
    }
}
