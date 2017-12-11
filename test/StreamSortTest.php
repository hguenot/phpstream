<?php

use phpstream\collectors\ListCollector;
use phpstream\Stream;

include_once (__DIR__ . '/comparators/ReverseComparator.php');

class StreamSortTest extends \PHPUnit\Framework\TestCase {

	public function testSort() {
		$array = [ 'l', 'e', 'z', 'a' ];
		$stream = new Stream($array);

		$res = $stream->sort()->collect(new ListCollector());
		
		$this->assertEquals([ 'a', 'e', 'l', 'z' ], $res);
	}
	
	public function testCallable() {
		$array = [ 2, 4, -2, -9 ];
		$stream = new Stream($array);

		$res = $stream->sort(function($first, $second){
			return (intval($first) - intval($second)) / abs(intval($first) - intval($second));
		})->collect(new ListCollector());
		
		$this->assertEquals([ -9, -2, 2, 4 ], $res);
	}
	
	public function testComparator() {
		$array = [ 2, 4, -2, -9 ];
		$stream = new Stream($array);

		$res = $stream->sort(new ReverseComparator())->collect(new ListCollector());
		
		$this->assertEquals([ 4, 2, -2, -9 ], $res);
	}
	
	public function testException() {
		try {
			$array = [ 2, 4, -2, -9 ];
			$stream = new Stream($array);
			$stream->sort('reverseComparator');
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
	
}
