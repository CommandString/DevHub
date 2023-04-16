<?php

namespace Common\Configuration;

use Common\MagicProps;

/**
 * @property bool $DEV_MODE
 * @property string $DOMAIN
 * @property string $DB_HOST
 * @property string $DB_NAME
 * @property string $DB_USERNAME
 * @property string $DB_PASSWORD
 * @property string $DB_PORT
 * @property \Tnapf\Pdo\Driver $driver
 * @property \Tnapf\MysqlSessions\Controller $sessionController
 * @property \Twig\Environment $twig
 */
class Env extends MagicProps
{
    protected static self $instance;

    /**
     * @throws \LogicException If an Environment has already been created
     */
    public function __construct()
    {
        if (isset(self::$instance)) {
            throw new \LogicException("An Environment has already been substantiated!");
        }

        self::$instance = $this;
    }

    public static function get(): ?self
    {
        return self::$instance ?? null;
    }

    public static function createFromEnv(string $path): self
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("{$path} is not an existing env file!");
        }

        $instance = new self();

        $contents = file_get_contents($path);
        $lines = explode("\n", $contents);

        foreach ($lines as $number => $line) {
            if (!strlen(trim($line))) {
                continue;
            }

            $number++; // make line number proper

            $parts = explode("=", $line);

            if (count($parts) !== 2) {
                throw new \RuntimeException("Line {$number} is not correct");
            }

            $name = trim($parts[0]);
            $value = trim($parts[1]);

            if (preg_match("/[^a-zA-Z0-9_]/", $name)) {
                throw new \RuntimeException("{$name} is not valid on line {$number}");
            }

            $instance->__set($name, $value);
        }

        return $instance;
    }

    public function __debuginfo()
    {
        return $this->props;
    }
}
