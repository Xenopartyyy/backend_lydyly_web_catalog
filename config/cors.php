<?php

return [
    /*
     * Paths yang akan menggunakan CORS
     */
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],

    /*
     * Methods yang diizinkan
     */
    'allowed_methods' => ['*'],

    /*
     * Origins yang diizinkan
     */
    'allowed_origins' => ['*'],
    // Untuk production, gunakan spesifik:
    // 'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],

    /*
     * Pattern origins yang diizinkan
     */
    'allowed_origins_patterns' => [],

    /*
     * Headers yang diizinkan
     */
    'allowed_headers' => ['*'],

    /*
     * Headers yang di-expose
     */
    'exposed_headers' => [],

    /*
     * Max age untuk preflight request
     */
    'max_age' => 0,

    /*
     * Support credentials (cookies, authorization headers)
     */
    'supports_credentials' => true,
];
