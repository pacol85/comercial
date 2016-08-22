<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        	$config->application->controllersDir,
        	$config->application->modelsDir,
    		$config->application->libraryDir."funciones.php",
    		$config->application->libraryDir."PHPExcel-1.8/Classes/"
    )
)->register();
