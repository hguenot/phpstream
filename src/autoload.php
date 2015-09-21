<?php

/**
 * Autoload register file.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

spl_autoload_register(function ($class) {
	$basePackage = 'phpstream\\';
	if (substr($class, 0, strlen($basePackage)) == $basePackage) {
	   include __DIR__ . DIRECTORY_SEPARATOR . ereg_replace('\\\\', DIRECTORY_SEPARATOR, $class) . '.php';
	}
});
