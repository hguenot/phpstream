<?php

use phpstream\Stream;

class StreamBasicTest extends PHPUnit_Framework_TestCase {

	public function testCountSimple() {
		$res = Stream::of([ 1, 2, 3, 4, 5, 6 ])->count();
		$this->assertEquals(6, $res);
	}
	
	public function testCountFilter() {
		$res = Stream::of([ 1, 2, 3, 4, 5, 6 ])->filter(function($value){
			return intval($value) % 2 == 0;
		})->count();
		
		$this->assertEquals(3, $res);
	}
	
	public function testToArray() {
		$res = Stream::of([ 'a' => 1, 'z' => 2, 'e' => 3, 'r' => 4, 't' => 5, 'y' => 6 ])->toArray();
		$this->assertEquals([ 1, 2, 3, 4, 5, 6 ], $res);
	}
	
	public function testToMap() {
		$res = Stream::of([ 'a' => 1, 'z' => 2, 'e' => 3, 'r' => 4, 't' => 5, 'y' => 6 ])->toMap();
		$this->assertEquals([ 'a' => 1, 'z' => 2, 'e' => 3, 'r' => 4, 't' => 5, 'y' => 6 ], $res);
	}
}
