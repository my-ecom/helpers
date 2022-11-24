<?php
namespace oangia\firebase;

use oangia\CUrl;

class FireStore {
    function __construct(Firebase $fb, $database = '(default)') {
        $this->fb = $fb;
        $this->database = $database;
    }

    public function getCollection($collection, $id) {
        $url = $this->fb->base_url . $this->fb->config['projectId'] . '/databases/' . $this->database . '/documents/' . $collection . '/' . $id . '?key=' . $this->fb->config['apiKey'];
        $curl = new CUrl();
        $response = $curl->connect('GET', $url);

        return $response;
    }

    /*

    $url = 'https://firestore.googleapis.com/v1/projects/myecom-f0a26/databases/(default)/documents/users?documentId=456&key=AIzaSyDBLyiGjroIhQndhe0T3iac39GalX-z9Lo';

    $start = time();
    $user_ip = getUserIP();
    $patch = 'https://firestore.googleapis.com/v1/projects/' . $project_id . '/databases/(default)/documents/clients/' . $user_ip . '?key=' . $web_api_key . '&updateMask.fieldPaths=last_access';

    $curl = new CUrl();
    $re = $curl->connect('GET', $url);
    echo time() - $start;
    dd($re);
    $curl->json_data();
    $curl->json();
    $data = [
    	'fields'=> [
    			'last_access'=> [ 'integerValue'=> time() ],
    	]
    ];
    $response = $curl->connect('PATCH', $patch, $data);
    echo time() - $start;
    dd($response);*/
}
