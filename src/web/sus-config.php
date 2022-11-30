<?php
use oangia\web\Route;

define( 'FIREBASE_API_KEY', 'AIzaSyDBLyiGjroIhQndhe0T3iac39GalX-z9Lo' );
define( 'FIREBASE_PROJECT_ID', 'myecom-f0a26' );
define( 'FIREBASE_SITE_ID', 'b937500118f'); // substr(md5('oangia_' . $_SERVER['SERVER_NAME']), 0, 11)
// use for Authentication rest api
define( 'OG_API_KEY', '123' );

Route::get('/', function() {
    echo 'Hello world';
});
Route::get('/product/{slug}', function($slug) {
    echo 'Product';
});
