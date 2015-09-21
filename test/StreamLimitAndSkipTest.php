<?php

use phpstream\Stream;
use phpstream\collectors\ListCollector;
use phpstream\collectors\MapCollector;

class StreamLimitAndSkipTest extends PHPUnit_Framework_TestCase {

	public function testLimit() {
		$array = [ 1, 2, 3, 4, 5, 6 ];
		$stream = new Stream($array);
		$stream->limit(2);
		
		$res = $stream->collect(new ListCollector());
		
		$this->assertEquals([ 1, 2 ], $res);
	}
	
	public function testSkip() {
		$array = [ 1, 2, 3, 4, 5, 6 ];
		$stream = new Stream($array);
		$stream->skip(4);
		
		$res = $stream->collect(new ListCollector());
		
		$this->assertEquals([ 5, 6 ], $res);
	}
	
	public function testLimitMapCollector() {
		$array = [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6 ];
		$stream = new Stream($array);
		$stream->limit(2);
		
		$res = $stream->collect(new MapCollector());
		
		$this->assertEquals([ 'a' => 1, 'b' => 2 ], $res);
	}
	
	
	public function testSkipMapCollector() {
		$array = [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6 ];
		$stream = new Stream($array);
		$stream->skip(3);
		
		$res = $stream->collect(new MapCollector());
		
		$this->assertEquals([ 'd' => 4, 'e' => 5, 'f' => 6 ], $res);
	}
	
}
