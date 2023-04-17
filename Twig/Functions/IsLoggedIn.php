<?php

namespace Twig\Functions;

use Tnapf\SessionInterfaces\Exceptions\SessionDoesNotExist;

use function Common\env;

class IsLoggedIn extends TwigFunction
{
    private static bool $isLoggedIn;

    public static function getName(): string
    {
        return "isLoggedIn";
    }

    public static function method(): bool {
        if (!isset(self::$isLoggedIn)) {
            try {
                env()->sessionController->get($_COOKIE['session_id'] ?? "");
            } catch (SessionDoesNotExist) {
                $return = false;
            }

            self::$isLoggedIn = $return ?? true;
        }

        return self::$isLoggedIn;
    }

    public static function getMethod(): callable
    {
        return fn(): bool => self::method();
    }
}
