<?php

namespace Twig\Functions;

class Entrypoint extends TwigFunction
{
    public static function getMethod(): callable
    {
        return function (string $name): string {
            $entrypointsLocation = __ASSETS__ . "/entrypoints.json";

            if (!file_exists($entrypointsLocation)) {
                throw new \RuntimeException("Cannot find ${entrypointsLocation}");
            }

            $entrypoints = json_decode(file_get_contents($entrypointsLocation))->entrypoints;

            if (!isset($entrypoints->{$name})) {
                throw new \InvalidArgumentException("{$name} is not a valid entry point");
            }

            $entrypoint = $entrypoints->{$name};

            $jsTemplate = "<script src='%s' defer></script>";
            $cssTemplate = "<link rel='stylesheet' href='%s'>";

            $return = "";

            foreach ($entrypoint->js as $path) {
                $return .= sprintf($jsTemplate, $path) . "\n";
            }

            foreach ($entrypoint->css as $path) {
                $return .= sprintf($cssTemplate, $path);
            }

            return $return;
        };
    }

    public static function getName(): string
    {
        return "entrypoint";
    }
}
