<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
    'https://dtqverse.io.vn',
    'https://dtqverse.onrender.com',
    'http://localhost:5173',
    'http://127.0.0.1:5173',
    'https://phamhuutai.io.vn',
    'https://www.phamhuutai.io.vn',
    'https://web-tracnghiem-fe.onrender.com',
    'https://web-tracnghiem.onrender.com',
],

    'allowed_origin_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];