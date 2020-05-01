<?php
require_once '../../vendor/autoload.php';

use phpstream\collectors\StreamCollector;
use phpstream\Stream;

$stream = Stream::of([ '0', 'aa', '1', 'bb', '2', 'cc', '3', 'dd', '4' ]);
$res = $stream
	// Converts every elements of the array into its int value
	->map(function($value){ return intval($value); })
	// Removes elements lower or egals to zero
	->filter(function($value){ return $value > 0; })
	// Removes odd elements
	->filter(function($value){ return $value % 2 == 0; })
	// Add 1 to each elements
	->map(function($value){ return $value + 1; })
	// Compute sum and returns the result
	->collect(new class() implements StreamCollector {
		/**
		 * @inheritDoc
		 */
		public function collect(iterable $values) {
			$v = 0;
			foreach ($values as $value) {
				$v += $value;
			}
			return $v;
		}
	});
	// Returns 8 !

var_dump($res);
