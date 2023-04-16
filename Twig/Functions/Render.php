<?php

namespace Twig\Functions;

use function Common\env;

class Render extends TwigFunction
{
    public static function getName(): string
    {
        return "render";
    }

    public static function getMethod(): callable
    {
        return function (string $path, array $context = []): string {
            return env()->twig->render(
                str_replace(".", "/", $path) . ".twig",
                $context
            );
        };
    }
}
