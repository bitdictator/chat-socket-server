<?php

return [
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'local_db_name',
    'DB_USER' => 'local_db_user',
    'DB_PASS' => 'local_db_pwd',
    'DB_CHARSET' => 'utf8',
    'SERVER_URI' => '0.0.0.0:8080',

    // full list of timezones can be found here https://www.php.net/manual/en/timezones.php
    // we are using the default here
    'TIMEZONE' => date_default_timezone_get(),

    // the necryption key used by your website when creating the encrypted auth details
    'CHAT_SOCKET_SERVER_CIPHER_KEY' => "sdkj478ksfdj83erhrui",

    // this is to be used with the OriginCheck which is not used
    'ALLOWED_DOMAINS' => [],

    'LOCAL_CERT_PATH' => '',    // Path to fullchain.pem
    'LOCAL_PK_PATH' => ''       // Path to privkey.pem
];
