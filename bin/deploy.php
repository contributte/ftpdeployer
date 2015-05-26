<?php

define('CWD', __DIR__);
define('ROOT', realpath(CWD . '/../../../..'));

// Load composer
if (@!include ROOT . '/vendor/autoload.php') {
    echo 'Composer autoload.php not found. Did you call composer install|update?';
    exit(1);
}

// Test files masks
$tests = [
    // root/deploy[.php]
    ROOT. '/deploy',
    ROOT. '/deploy.php',

    // root/bin/deploy[.php]
    ROOT . '/bin/deploy',
    ROOT . '/bin/deploy.php',

    // root/bin/deploy[.php]
    ROOT . '/deploy/deploy',
    ROOT . '/deploy/deploy.php',
];

// Test files
foreach ($tests as $test) {
    if (file_exists($test)) {
        require $test;
        break;
    }
}

// Print info
foreach ($tests as $test) {
    echo "\t- $test\n";
}

echo 'Deploy file not found. Please run deploy by yourself.';
exit(1);
