<?php

namespace Common;

use CommandString\Utils\ArrayUtils;
use Common\Configuration\Env;
use HttpSoft\Response\HtmlResponse;
use Tnapf\Pdo\Driver;

function render(string $path, array $context = []): HtmlResponse
{
    return new HtmlResponse(
        renderHtml($path, $context)
    );
}

function renderHtml(string $path, array $context = []): string
{
    $html = env()->twig->render(
        str_replace(".", "/", $path) . ".twig",
        $context
    );

    if (!env()->DEV_MODE) {
        $html = implode("", ArrayUtils::trimValues(explode("\n", $html)));
    }

    return $html;
}

function env(): Env
{
    return Env::get();
}

function driver(): Driver
{
    return env()->driver;
}

function getMimeFromExtension(string $extensionToFindMimeFor): ?string
{
    $mimes = json_decode(file_get_contents(__ROOT__ . "/mimes.json"));

    foreach ($mimes as $mime => $extensions) {
        foreach ($extensions as $extension) {
            if ($extension == $extensionToFindMimeFor) {
                return $mime;
            }
        }
    }

    return null;
}
