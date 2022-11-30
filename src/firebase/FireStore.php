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
        $fields = $this->generateFields($data);
        $url = $this->fb->urlPost($this->database, $collection, $id);
        $curl = new CUrl();
        $curl->json_data();
        $curl->json();
        $response = $curl->connect('POST', $url, $fields);

        return $response;
    }

    public function setDoc($collection, $data, $id) {
        $fields = $this->generateFields($data);
        $url = $this->fb->urlGet($this->database, $collection, $id);// . '&updateMask.fieldPaths=last_access';
        $curl = new CUrl();
        $curl->json_data();
        $curl->json();
        $response = $curl->connect('PATCH', $url, $fields);

        return $response;
    }

    public function deleteDoc($collection, $id) {
        $url = $this->fb->urlGet($this->database, $collection, $id);
        $curl = new CUrl();
        $response = $curl->connect('DELETE', $url);

        return $response;
    }

    private function generateFields($data) {
        $fields = ['fields' => [

        ]];
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $fields['fields'][$key] = ['integerValue' => $value];
            } elseif(is_numeric($value)) {
                $fields['fields'][$key] = ['doubleValue' => $value];
            } else {
                $fields['fields'][$key] = ['stringValue' => $value];
            }
        }
        return $fields;
    }
}
