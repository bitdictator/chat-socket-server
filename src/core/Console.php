<?php

namespace Core;

class Console
{

    const COLOR_WHITE = 97;
    const COLOR_GREEN = 92;
    const COLOR_BLUE = 34;
    const COLOR_RED = 31;
    const COLOR_YELLOW = 33;

    /**
     * Outputs a message to the server console
     */
    public static function out(string $output_text, int $foreground_color_code = self::COLOR_WHITE)
    {
        // output the message in new line and reset the color to default (white)
        echo "\e[{$foreground_color_code}m" . $output_text . "\e[0m\n";
        return;
    }
}
