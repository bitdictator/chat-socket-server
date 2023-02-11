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

    // this is the encryption key that is used by the server the issues the auth details
    'ENCRYPTION_KEY' => '1234',

    // this is to be used with the OriginCheck which is not used
    'ALLOWED_DOMAINS' => []
];
