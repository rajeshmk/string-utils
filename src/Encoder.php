<?php

namespace CodeArtery\String;

class Encoder
{
    public static function uniqueHash(int $length = 40)
    {
        // The `additional entropy` part will always be decimal
        [$hex, $decimal] = explode('.', uniqid('', true));

        $base36 = strrev(base_convert($hex, 16, 36)).base_convert($decimal, 10, 36);

        // Ensure minimum length of 32 characters
        $pad_length = max(32, $length) - strlen($base36);

        $pad_string = '';
        while (($len = strlen($pad_string)) < $pad_length) {
            $size = $pad_length - $len;

            $bytesSize = (int) ceil($size / 3) * 3;

            $bytes = random_bytes($bytesSize);

            $pad_string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $base36.$pad_string;
    }
}
