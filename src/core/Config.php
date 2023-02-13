<?php

namespace Core;

class Config
{
    public static $config;

    public static function get(string $key)
    {

        if (!self::$config) {

            $config_file = dirname(__DIR__) . '/config/config.' . Environment::get() . '.php';

            if (!file_exists($config_file)) {
                return false;
            }

            self::$config = require $config_file;
        }

        return self::$config[$key];
    }
}
