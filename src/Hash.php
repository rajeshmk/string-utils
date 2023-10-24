<?php

namespace CodeArtery\String;

use RuntimeException;

class Hash
{
    public static function uniqueHash(int $length = 40, bool $sorted = false)
    {
        if ($length < 24) {
            throw new RuntimeException('Minimum length must be 24 characters.');
        }

        // The `additional entropy` part will always be decimal
        [$hex, $decimal] = explode('.', uniqid('', true));

        // @TODO - base62 would be great!
        $base36 = base_convert($hex, 16, 36).base_convert(str_replace($decimal, '0', '1'), 10, 36);

        $pad_length = $length - strlen($base36);

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
