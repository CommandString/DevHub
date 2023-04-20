<?php ob_start(); ?>
# Packages

<?php

function decode_file(string $path): stdClass
{
    $file = file_get_contents($path);
    $json = json_decode($file);
    return $json;
}

$npm = decode_file('package.json');
$composer = decode_file('composer.json');

?>
## NPM

<?php foreach ($npm->dependencies as $name => $version) { ?>
* [<?= $name ?>: <?= $version ?>](https://www.npmjs.com/package/<?= $name ?>)
<?php } ?>

## Composer

<?php foreach ($composer->require as $name => $version) { ?>
* [<?= $name ?>: <?= $version ?>](https://packagist.org/packages/<?= $name ?>)
<?php } ?>
<?php file_put_contents(__DIR__ . "/packages.md", ob_get_clean()); ?>