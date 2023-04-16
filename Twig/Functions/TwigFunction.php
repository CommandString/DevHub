<?php

namespace Twig\Functions;

use function Common\env;

abstract class TwigFunction
{
    abstract public static function getName(): string;

    abstract public static function getMethod(): callable;

    public static function add()
    {
        env()->twig->addFunction(
            new \Twig\TwigFunction(
                static::getName(),
                static::getMethod()
            )
        );
    }
}
