<?php

namespace Core;

class Config
{
    // TOOD: move this from here
    const CHAT_SOCKET_SERVER_JWT_KEY = "kjKFelnXMO7n82XDHONx3j9";

    public static $config;

    public static function get(string $key)
    {

        if (!self::$config) {

            $config_file = dirname(__DIR__) . '/config/config.' . Environment::get() . '.php';

            var_dump(dirname(__DIR__));
            exit;

            if (!file_exists($config_file)) {
                return false;
            }

            self::$config = require $config_file;
        }

        return self::$config[$key];
    }
}
