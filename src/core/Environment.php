<?php

namespace Core;

class Environment
{
    const DEV = "DEV";
    const PROD = "PROD";

    // change this on production
    private const CURRENT_ENVIRONMENT = self::DEV;

    public static function get()
    {
        return self::CURRENT_ENVIRONMENT;
    }
}
