<?php

namespace CodeArtery\String;

use RuntimeException;

class Hash
{
    public static function uniqueHash(int $length = 40): string
    {
        if ($length < 24) {
            throw new RuntimeException('Minimum length must be 24 characters.');
        }

        // The `additional entropy` part will always be decimal
        [$hexTimestamp, $microDecimal] = explode('.', uniqid('', true));

        // Monotonic time prefix
        $time36 = base_convert($hexTimestamp, 16, 36)
            . base_convert('1' . $microDecimal, 10, 36);

        $needRandom = $length - strlen($time36);

        $pad = '';
        while (($len = strlen($pad)) < $needRandom) {
            $remaining = $needRandom - $len;

            $bytesNeeded = (int) ceil($remaining * 6 / 8);
            $bytes = random_bytes(max(1, $bytesNeeded));

            $pad .= substr(strtr(base64_encode($bytes), '+/=', '012'), 0, $remaining);
        }

        return $time36 . strtolower($pad);
    }
}
