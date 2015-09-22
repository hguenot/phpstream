<?php

use phpstream\Stream;

include_once(__DIR__ . '/comparators/ReverseComparator.php');

class StreamMinMaxTest extends PHPUnit_Framework_TestCase {

	public function testMinNoParam() {
		$array = [ 3, 4, 1, 2, 5, 6 ];
		$stream = new Stream($array);

		$res = $stream->min();
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(1, $res->get());
	}
	
	public function testMinCallable() {
		$array = [ 3, 4, 1, 5, 6, 2 ];
		$stream = new Stream($array);

		$res = $stream->min(function($first, $second){
			return (intval($second) - intval($first)) / abs(intval($second) - intval($first));
		});
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(6, $res->get());
	}
	
	public function testMinComparator() {
		$array = [ 3, 4, 1, 5, 6, 2 ];
		$stream = new Stream($array);

		$res = $stream->min(new ReverseComparator());
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(6, $res->get());
	}
	
	public function testMinEmptyArray() {
		$array = [ ];
		$stream = new Stream($array);

		$res = $stream->min(new ReverseComparator());
		
		$this->assertTrue($res->isEmpty());
	}
	
	public function testMaxNoParam() {
		$array = [ 3, 4, 1, 5, 6, 2 ];
		$stream = new Stream($array);

		$res = $stream->max();
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(6, $res->get());
	}
	
	public function testMaxCallable() {
		$array = [ 3, 4, 1, 5, 6, 2 ];
		$stream = new Stream($array);

		$res = $stream->max(function($first, $second){
			return (intval($second) - intval($first)) / abs(intval($second) - intval($first));
		});
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(1, $res->get());
	}
	
	public function testMaxComparator() {
		$array = [ 3, 4, 1, 5, 6, 2 ];
		$stream = new Stream($array);

		$res = $stream->max(new ReverseComparator());
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(1, $res->get());
	}
	
	public function testMaxEmptyArray() {
		$array = [ ];
		$stream = new Stream($array);

		$res = $stream->max(new ReverseComparator());
		
		$this->assertTrue($res->isEmpty());
	}
	
	public function testMinException() {
		try {
			$array = [ ];
			$stream = new Stream($array);
			$stream->min('reverseComparator');
		} catch (\InvalidArgumentException $ex) {
			return;
		}
		
		$this->fail('An expected exception has not been raised.');
	}
	
	public function testMaxException() {
		try {
			$array = [ ];
			$stream = new Stream($array);
			$stream->max('reverseComparator');
		} catch (\InvalidArgumentException $ex) {
			return;
		}
		
		$this->fail('An expected exception has not been raised.');
	}
	
}
