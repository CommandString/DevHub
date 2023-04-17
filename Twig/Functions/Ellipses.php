<?php

namespace Twig\Functions;

class Ellipses extends TwigFunction
{
    public static function getName(): string
    {
        return "ellipses";
    }

    public static function method(string $string, int $length): string {
        $split = str_split($string, $length-3);

        return count($split) > 1 ? trim($split[0]).'...' : $string;
    }

    public static function getMethod(): callable
    {
        return fn($string, $length) => self::method($string, $length);
    }
}
