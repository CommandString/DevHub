<?php

namespace Twig\Functions;

class Ellipses extends TwigFunction
{
    public static function getName(): string
    {
        return "ellipses";
    }

    public static function getMethod(): callable
    {
        return function (string $string, int $length): string {
            $split = str_split($string, $length-3);

            return count($split) > 1 ? trim($split[0]).'...' : $string;
        };
    }
}
