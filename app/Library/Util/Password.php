<?php

namespace App\Library\Util;

use Phalcon\Text;

class Password
{

    public static function salt()
    {
        return Text::random(Text::RANDOM_ALNUM, 8);
    }

    public static function hash($password, $salt)
    {
        return md5(md5($password) . md5($salt));
    }

    public static function checkHash($password, $salt, $passwordHash)
    {
        $inputHash = self::hash($password, $salt);

        return $inputHash == $passwordHash;
    }

}
