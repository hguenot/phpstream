<?php
require_once '../../src/autoload.php';

use phpstream\Stream;

$stream = new Stream([ '0', 'aa', '1', 'bb', '2', 'cc', '3', 'dd', '4' ]);
$res = $stream
	// Converts every elements of the array into its int value
	->map(function($value){ return intval($value); })
	// Removes elements lower or egals to zero
	->filter(function($value){ return $value > 0; })
	// Removes odd elements 
	->filter(function($value){ return $value % 2 == 0; })
	// Add 1 to each elements
	->map(function($value){ return $value + 1; })
	// Compute sum and returns a phpstream\util\Optional value
	->reduceWithDefault(function($acc, $v) { return $acc + $v; }, 0);
	// Returns 8 !

var_dump($res);
