<?php
namespace oangia\web\database;

class Seed {
    public static function run() {
        $faker = Faker\Factory::create();
        dd($faker->name);
        $data = [
            'title' => 'Alessandro Michele\'s Best Gucci Looks in Street Style',
            'slug' =>
        ]
        Database::query('INSERT INTO posts () VALUES()')
    }
}
