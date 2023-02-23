<?php

return [
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'production_db_host_url',
    'DB_NAME' => 'prod_db_name',
    'DB_USER' => 'prod_db_user',
    'DB_PASS' => 'prod_db_pwd',
    'DB_CHARSET' => 'utf8',
    'SERVER_URI' => 'tls://0.0.0.0:443',

    // full list of timezones can be found here https://www.php.net/manual/en/timezones.php
    // we are using the default here
    'TIMEZONE' => date_default_timezone_get(),

    // the necryption key used by your website when creating the encrypted auth details
    'ENCRYPTION_KEY' => '1234',

    // this is to be used with the OriginCheck which is not used
    'ALLOWED_DOMAINS' => []
];
