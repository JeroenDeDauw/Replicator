<?php

if ( file_exists( $a = __DIR__.'/../../autoload.php' ) ) {
	require_once $a;
} else {
	require_once __DIR__.'/../vendor/autoload.php';
}

use Queryr\Replicator\Application;

$console = new Application();
$console->run();