<?php

use phpstream\Stream;
use phpstream\collectors\ListCollector;
use phpstream\collectors\MapCollector;

include_once(__DIR__ . '/functions/SquareFunction.php');

class StreamMapTest extends PHPUnit_Framework_TestCase {

	public function testCallable() {
		$array = [ 1, 2, 3, 4, 5, 6 ];
		$stream = new Stream($array);
		$stream->map(function($value){
			return intval($value) * intval($value);
		});
		
		$res = $stream->collect(new ListCollector());
		
		$this->assertEquals([ 1, 4, 9, 16, 25, 36 ], $res);
	}
	
	public function testFunction() {
		$array = [ 1, 2, 3, 4, 5, 6 ];
		$stream = new Stream($array);
		$stream->map(new SquareFunction());
		
		$res = $stream->collect(new ListCollector());
		
		$this->assertEquals([ 1, 4, 9, 16, 25, 36 ], $res);
	}
	
	public function testMapCollector() {
		$array = [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6 ];
		$stream = new Stream($array);
		$stream->map(new SquareFunction());
		
		$res = $stream->collect(new MapCollector());
		
		$this->assertEquals([ 'a' => 1, 'b' => 4, 'c' => 9, 'd' => 16, 'e' => 25, 'f' => 36 ], $res);
	}
	
}
