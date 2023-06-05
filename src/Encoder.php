<?php

namespace CodeArtery\String;

class Encoder
{
    public static function uniqueHash(int $length = 40, bool $sorted = false)
    {
        // The `additional entropy` part will always be decimal
        [$hex, $decimal] = explode('.', uniqid('', true));

        $string = $hex.dechex((int) $decimal);

        // @TODO - base62 would be great!
        $base36 = base_convert($string, 16, 36);

        // Ensure minimum length of 32 characters
        $pad_length = max(32, $length) - strlen($base36);

        $pad_string = '';
        while (($len = strlen($pad_string)) < $pad_length) {
            $size = $pad_length - $len;

            $bytesSize = (int) ceil($size / 3) * 3;

            $bytes = random_bytes($bytesSize);

            $pad_string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $sorted ? $base36.$pad_string : $pad_string.$base36;
    }
}
