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
        if (!isset($data['createTime'])) {
            $data['createTime'] = time();
        }
        if (! isset($data['updateTime'])) {
            $data['updateTime'] = time();
        }
        $fields = $this->generateFields($data);
        $url = $this->fb->urlPost($this->database, $collection, $id);
        $curl = new CUrl();
        $curl->json_data();
        $curl->json();
        $response = $curl->connect('POST', $url, $fields);
        return $response;
    }

    public function setDoc($collection, $data, $id) {
        $mask = [];
        foreach ($data as $key => $value) {
            $mask[] = 'updateMask.fieldPaths=' . $key;
        }
        $fields = $this->generateFields($data);
        $url = $this->fb->urlGet($this->database, $collection, $id) . '&' . implode('&', $mask);
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

    public function runQuery($document, $query) {
        $url = $this->fb->urlQuery($this->database, $document);
        $curl = new CUrl();
        $curl->json_data();
        $curl->json();
        $response = $curl->connect('POST', $url, $query);
        return $response;
    }

    private function generateFields($data) {
        $fields = ['fields' => [
        ]];
        foreach ($data as $key => $value) {
            if (intval($value) === $value) {
                $fields['fields'][$key] = ['integerValue' => intval($value)];
            } elseif(doubleval($value) === $value) {
                $fields['fields'][$key] = ['doubleValue' => doubleval($value)];
            } elseif(boolval($value) === $value) {
                $fields['fields'][$key] = ['booleanValue' => boolval($value)];
            } elseif (is_array($value)) {
                if ($this->isAssoc($value)) {
                    $fields['fields'][$key] = ['mapValue' => $this->generateFields($value)];
                } else {
                    $fields['fields'][$key] = ['arrayValue' => $this->generateArrayValues($value)];
                }
            } else {
                $fields['fields'][$key] = ['stringValue' => strval($value)];
            }
        }
        return $fields;
    }

    private function generateArrayValues($data) {
        $values = ['values' => [
        ]];
        foreach ($data as $key => $value) {
            if (intval($value) === $value) {
                $values['values'][] = ['integerValue' => intval($value)];
            } elseif(doubleval($value) === $value) {
                $values['values'][] = ['doubleValue' => doubleval($value)];
            } elseif(boolval($value) === $value) {
                $values['values'][] = ['booleanValue' => boolval($value)];
            } elseif (is_array($value)) {
                if ($this->isAssoc($value)) {
                    $values['values'][] = ['mapValue' => $this->generateFields($value)];
                } else {
                    $values['values'][] = ['arrayValue' => $this->generateArrayValues($value)];
                }
            } else {
                $values['values'][] = ['stringValue' => strval($value)];
            }
        }
        return $values;
    }

    function isAssoc($arr)
    {
        if (! is_array($arr)) return false;
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
