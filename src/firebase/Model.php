<?php
namespace oangia\firebase;

use oangia\web\Response;

class Model {
    function __construct() {
        $this->fb = new Firebase;
        $this->db = new FireStore($this->fb);
    }

    public function find($collection, $id) {
        $response = json_decode($this->db->getDoc($collection, $id), true);
        if (isset($response['error'])) {
            return $response;
        }
        return $this->convertData($response);
    }

    public function create($collection, $data, $id = '') {
        $response = json_decode($this->db->addDoc($collection, $data, $id), true);
        if (isset($response['error'])) {
            return $response;
        }
        $data = $this->convertData($response);
        $id = explode('/', $response['name']);
        $data['id'] = end($id);
        return $data;
    }

    public function update($collection, $data, $id) {
        $response = json_decode($this->db->setDoc($collection, $data, $id), true);
        if (isset($response['error'])) {
            return $response;
        }
        return $this->convertData($response);
    }

    public function delete($collection, $id) {
        $response = $this->db->deleteDoc($collection, $id);
        if (isset($response['error'])) {
            return $response;
        }
        return true;
    }

    public function list($document, $table, $orderBy = 'createTime', $limit = 8) {
        $query = [
            "structuredQuery" => [
                "from" => [
                    ["collectionId" => $table]
                ],
                "orderBy" => [
                    [
                        "field" => ["fieldPath" => $orderBy],
                        "direction" => "DESCENDING"
                    ]
                ],
                /*"where" => [
                    "compositeFilter" => [
                        "filters" => [
                            [
                                "fieldFilter" => [
                                    "field" => [
                                        "fieldPath" => "createTime"
                                    ],
                                    "op" => "GREATER_THAN",
                                    "value" => [
                                        "integerValue" => 0
                                    ]
                                ]
                            ]
                        ],
                        "op" => "AND"
                    ]
                ],*/
                "limit" => $limit
            ]
        ];
        $response = json_decode($this->runQuery($document, $query), true);
        if (isset($response['error'])) {
            return $response;
        }
        $result = [];
        foreach ($response as $item) {
            $result[] = $this->convertData($item['document']);
        }
        return $result;
    }

    public function runQuery($document, $query) {
        return $this->db->runQuery($document, $query);
    }

    private function convertData($data) {
        $result = [];
        foreach ($data['fields'] as $key => $value) {
            foreach ($value as $type => $realValue) {
                switch ($type) {
                    case 'arrayValue':
                        $result[$key] = $this->convertArray($value[$type]);
                        break;
                    case 'mapValue':
                        $result[$key] = $this->convertData($value[$type]);
                        break;
                    default:
                        $result[$key] = $value[$type];
                }
            }
        }
        return $result;
    }

    private function convertArray($data) {
        $result = [];
        foreach ($data['values'] as $key => $value) {
            foreach ($value as $type => $realValue) {
                switch ($type) {
                    case 'arrayValue':
                        $result[$key] = $this->convertArray($value[$type]);
                        break;
                    case 'mapValue':
                        $result[$key] = $this->convertData($value[$type]);
                        break;
                    default:
                        $result[$key] = $value[$type];
                }
            }
        }
        return $result;
    }
}
