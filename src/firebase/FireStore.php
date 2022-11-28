<?php
namespace oangia\firebase;

use oangia\CUrl;

class FireStore {
    function __construct(Firebase $fb, $database = '(default)') {
        $this->fb = $fb;
        $this->database = $database;
    }

    public function getDoc($collection, $id) {
        $url = $this->fb->urlGet($this->database, $collection, $id);
        $curl = new CUrl();
        $response = $curl->connect('GET', $url);

        return $response;
    }

    public function addDoc($collection, $data, $id = '') {
        $url = $this->fb->urlPost($this->database, $collection, $id);
        $curl = new CUrl();
        $curl->json_data();
        $curl->json();
        $response = $curl->connect('POST', $url, $data);

        return $response;
    }

    public function setDoc($collection, $data, $id) {
        $url = $this->fb->urlGet($this->database, $collection, $id);// . '&updateMask.fieldPaths=last_access';
        $curl = new CUrl();
        $curl->json_data();
        $curl->json();
        $response = $curl->connect('PATCH', $url, $data);

        return $response;
    }

    public function deleteDoc($collection, $id) {
        $url = $this->fb->urlGet($this->database, $collection, $id);
        $curl = new CUrl();
        $response = $curl->connect('DELETE', $url);

        return $response;
    }
}
