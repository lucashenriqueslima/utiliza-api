<?php

use Illuminate\Support\Str;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'locavibe' => [
            'driver' => 'mongodb',
            'host' => env('LOCAVIBE_DB_HOST', '127.0.0.1'),
            'port' => env('LOCAVIBE_DB_PORT', '27017'),
            'dsn' => env('LOCAVIBE_DB_DSN'),
            'database' => env('LOCAVIBE_DB_DATABASE', 'locavibe'),
            'username' => env('LOCAVIBE_DB_USERNAME', 'root'),
            'password' => env('LOCAVIBE_DB_PASSWORD', '123456'),
            'options' => [],
        ],

        'ileva' => [
            'driver' => 'mysql',
            'url' => env('ILEVA_DB_URL'),
            'host' => env('ILEVA_DB_HOST', '127.0.0.1'),
            'port' => env('ILEVA_DB_PORT', '3306'),
            'database' => env('ILEVA_DB_DATABASE', 'laravel'),
            'username' => env('ILEVA_DB_USERNAME', 'root'),
            'password' => env('ILEVA_DB_PASSWORD', ''),
            'unix_socket' => env('ILEVA_DB_SOCKET', ''),
            'charset' => env('ILEVA_DB_CHARSET', 'utf8mb4'),
            'collation' => env('ILEVA_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'ileva_motoclub' => [
            'driver' => 'mysql',
            'url' => env('ILEVA_MOTOCLUB_DB_URL'),
            'host' => env('ILEVA_MOTOCLUB_DB_HOST', '127.0.0.1'),
            'port' => env('ILEVA_MOTOCLUB_DB_PORT', '3306'),
            'database' => env('ILEVA_MOTOCLUB_DB_DATABASE', 'laravel'),
            'username' => env('ILEVA_MOTOCLUB_DB_USERNAME', 'root'),
            'password' => env('ILEVA_MOTOCLUB_DB_PASSWORD', ''),
            'unix_socket' => env('ILEVA_MOTOCLUB_DB_SOCKET', ''),
            'charset' => env('ILEVA_MOTOCLUB_DB_CHARSET', 'utf8mb4'),
            'collation' => env('ILEVA_MOTOCLUB_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'ileva_nova' => [
            'driver' => 'mysql',
            'url' => env('ILEVA_NOVA_DB_URL'),
            'host' => env('ILEVA_NOVA_DB_HOST', '127.0.0.1'),
            'port' => env('ILEVA_NOVA_DB_PORT', '3306'),
            'database' => env('ILEVA_NOVA_DB_DATABASE', 'laravel'),
            'username' => env('ILEVA_NOVA_DB_USERNAME', 'root'),
            'password' => env('ILEVA_NOVA_DB_PASSWORD', ''),
            'unix_socket' => env('ILEVA_NOVA_DB_SOCKET', ''),
            'charset' => env('ILEVA_NOVA_DB_CHARSET', 'utf8mb4'),
            'collation' => env('ILEVA_NOVA_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'ileva_aupet' => [
            'driver' => 'mysql',
            'url' => env('ILEVA_AUPET_DB_URL'),
            'host' => env('ILEVA_AUPET_DB_HOST', '127.0.0.1'),
            'port' => env('ILEVA_AUPET_DB_PORT', '3306'),
            'database' => env('ILEVA_AUPET_DB_DATABASE', 'laravel'),
            'username' => env('ILEVA_AUPET_DB_USERNAME', 'root'),
            'password' => env('ILEVA_AUPET_DB_PASSWORD', ''),
            'unix_socket' => env('ILEVA_AUPET_DB_SOCKET', ''),
            'charset' => env('ILEVA_AUPET_DB_CHARSET', 'utf8mb4'),
            'collation' => env('ILEVA_AUPET_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
