<?php

namespace Twig\Functions;

use Common\Database\User;
use Tnapf\SessionInterfaces\Exceptions\SessionDoesNotExist;

use function Common\env;

class GetCurrentUser extends TwigFunction
{
    private static User $user;

    public static function getName(): string
    {
        return "getCurrentUser";
    }

    public static function getMethod(): callable
    {
        return function (): ?User {
            if (!isset(self::$user)) {
                try {
                    $session = env()->sessionController->get($_COOKIE['session_id'] ?? "");
                    $user = User::fetchById($session->get("user_id"));
                } catch (SessionDoesNotExist) {
                    return null;
                }

                self::$user = $user;
            }

            return self::$user;
        };
    }
}
