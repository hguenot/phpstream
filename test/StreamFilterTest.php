<?php

use phpstream\Stream;
use phpstream\collectors\ListCollector;
use phpstream\collectors\MapCollector;

include_once(__DIR__ . '/functions/EvenFunction.php');

class StreamFilterTest extends PHPUnit_Framework_TestCase {

	public function testCallable() {
		$array = [ 1, 2, 3, 4, 5, 6 ];
		$stream = new Stream($array);
		$stream->filter(function($value){
			return intval($value) % 2 == 0;
		});
		
		$res = $stream->collect(new ListCollector());
		
		$this->assertEquals([ 2, 4, 6 ], $res);
	}
	
	public function testFunction() {
		$array = [ 1, 2, 3, 4, 5, 6 ];
		$stream = new Stream($array);
		$stream->filter(new EvenFunction());
		
		$res = $stream->collect(new ListCollector());
		
		$this->assertEquals([ 2, 4, 6 ], $res);
	}
	
	public function testMapCollector() {
		$array = [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6 ];
		$stream = new Stream($array);
		$stream->filter(function($value){
			return intval($value) % 2 == 0;
		});
		
		$res = $stream->collect(new MapCollector());
		
		$this->assertEquals([ 'b' => 2, 'd' => 4, 'f' => 6 ], $res);
	}
	
}
