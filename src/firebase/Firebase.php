<?php
namespace oangia\firebase;

class Firebase {
    public $base_url = 'https://firestore.googleapis.com/v1/projects/';

    function __construct($config) {
        $this->config = $config;
    }


}
