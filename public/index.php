<?php

use Common\Configuration\Env;
use Common\Database\User;
use Tnapf\Pdo\Driver;
use Tnapf\Router\Router;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function Common\getMimeFromExtension;

define("__ROOT__", realpath(__DIR__ . "/../"));

require_once __ROOT__ . '/app/Constants.php';
require_once __ROOT__ . '/vendor/autoload.php';

if (PHP_SAPI === "cli-server") {
    $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $path = pathinfo($url);
    if (!empty($path["extension"])) {
        $file_path = __PUBLIC__."{$path['dirname']}/{$path["basename"]}";

        if (file_exists($file_path)) {
            header("content-type: ".getMimeFromExtension($path["extension"]));
            readfile($file_path);
            return;
        }
    }
}

// Setup Environment
$env = Env::createFromEnv(__ROOT__ . "/.env");
$env->twig = new Environment(
    new FilesystemLoader([__ROOT__ . "/views/"])
);
$env->driver = Driver::createMySqlDriver(
    $env->DB_USERNAME,
    $env->DB_PASSWORD,
    $env->DB_NAME,
    $env->DB_HOST,
    $env->DB_PORT
)->connect();
$env->sessionController = new \Tnapf\MysqlSessions\Controller($env->driver->driver);

define('__DOMAIN__', $env->DOMAIN);

require_once __ROOT__ . '/app/Routes.php';
require_once __ROOT__ . '/app/TwigExtensions.php';

Router::run();
