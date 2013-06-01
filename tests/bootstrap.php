<?php

$basedir = realpath(__DIR__ . '/../../../../');
$includePath = array(
    $basedir,
    $basedir . '/lib/',
    $basedir . '/app/',
    get_include_path()
);
unset($basedir);
set_include_path(implode(PATH_SEPARATOR, $includePath));

require_once 'MageTest/autoload.php';
