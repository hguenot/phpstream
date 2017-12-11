<?php

use phpstream\collectors\ListCollector;
use phpstream\collectors\MapCollector;
use phpstream\Stream;

class StreamDistinctTest extends \PHPUnit\Framework\TestCase {

	public function testList() {
		$array = [ 1, 2, 2, 3, 4, 4, 5, 6 ];
		$stream = new Stream($array);
		$res = $stream->distinct()->collect(new ListCollector());
		
		$this->assertEquals([ 1, 2, 3, 4, 5, 6 ], $res);
	}
	
	public function testMap() {
		$array = [ 
			'a' => 1, 
			'b' => 2, 
			'c' => 2, 
			'd' => 3, 
			'e' => 4, 
			'f' => 4, 
			'g' => 5, 
			'h' => 6 
		];
		$stream = new Stream($array);
		$res = $stream->distinct()->collect(new MapCollector());
		
		$this->assertEquals([ 
			'a' => 1, 
			'b' => 2, 
			'd' => 3, 
			'e' => 4, 
			'g' => 5, 
			'h' => 6 
		], $res);
	}
	
}
