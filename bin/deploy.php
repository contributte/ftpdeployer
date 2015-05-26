<?php

define('CWD', __DIR__);

// Load composer
if (@!include __DIR__ . '/../vendor/autoload.php') {
    echo 'Composer autoload.php not found. Did you call composer install|update?';
    exit(1);
}

// Test files masks
$tests = [
    // root/deploy[.php]
    realpath(CWD . '/../../deploy'),
    realpath(CWD . '/../../deploy.php'),

    // root/bin/deploy[.php]
    realpath(CWD . '/../../bin/deploy'),
    realpath(CWD . '/../../bin/deploy.php'),

    // root/bin/deploy[.php]
    realpath(CWD . '/../../deploy/deploy'),
    realpath(CWD . '/../../deploy/deploy.php'),
];

// Test files
foreach ($tests as $test) {
    if (file_exists($tests)) {
        require $test;
        break;
    }
}

// Print info
echo "Tried files:\n";
foreach ($tests as $test) {
    echo "\t- $test\n";
}

echo 'Deploy file not found. Please run deploy by yourself.';
exit(1);