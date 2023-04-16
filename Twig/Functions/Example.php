<?php

namespace Twig\Functions;

class Example extends TwigFunction
{
    public static function getName(): string
    {
        return "example";
    }

    public static function getMethod(): callable
    {
        return function (): string {
            return "Hi";
        };
    }
}
