<?php

use phpstream\Stream;

class StreamFindTest extends PHPUnit_Framework_TestCase {

	public function testFirstNoData() {
		$array = [ ];
		$stream = new Stream($array);
		$res = $stream->findFirst();
		
		$this->assertTrue($res->isEmpty());
	}
	
	public function testFirstReturningData() {
		$array = [ 1, 2, 3, 4, 5, 6 ];
		$stream = new Stream($array);
		$res = $stream->findFirst();
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(1, $res->get());
	}
	
	public function testAnyNoData() {
		$array = [ ];
		$stream = new Stream($array);
		$res = $stream->findAny();
		
		$this->assertTrue($res->isEmpty());
	}
	
	public function testAnyReturningData() {
		$array = [ 1, 2, 3, 4, 5, 6 ];
		$stream = new Stream($array);
		$res = $stream->findAny();
		
		$this->assertFalse($res->isEmpty());
		$this->assertTrue(in_array($res->get(), $array, true));
	}
	
}
