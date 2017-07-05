<?php
return [
    'connection' =>  [
        'pgsql' => [
            'driver' => 'cassandra',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '9042'),
            'keyspace' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', 'cassandra'),
            'password' => env('DB_PASSWORD', 'cassandra'),
        ]
    ]
];
