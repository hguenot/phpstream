<?php

use phpstream\Stream;
use phpstream\collectors\ListCollector;
use phpstream\collectors\MapCollector;

include_once(__DIR__ . '/functions/SquareFunction.php');

class StreamPeekTest extends PHPUnit_Framework_TestCase {

	public function testCallable() {
		$array = [ 1, 2, 3, 4, 5, 6 ];
		$stream = new Stream($array);
		$stream->peek(function($value){
			return intval($value) * intval($value);
		});
		
		$res = $stream->collect(new ListCollector());
		
		$this->assertEquals([ 1, 2, 3, 4, 5, 6 ], $res);
	}
	
	public function testFunction() {
		$array = [ 1, 2, 3, 4, 5, 6 ];
		$stream = new Stream($array);
		$stream->peek(new SquareFunction());
		
		$res = $stream->collect(new ListCollector());
		
		$this->assertEquals([ 1, 2, 3, 4, 5, 6 ], $res);
	}
	
	public function testMapCollector() {
		$array = [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6 ];
		$stream = new Stream($array);
		$stream->peek(new SquareFunction());
		
		$res = $stream->collect(new MapCollector());
		
		$this->assertEquals([ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6 ], $res);
	}
	
	public function testNotCallable() {
		try {
			Stream::of()->peek(true);
		} catch (\InvalidArgumentException $ex) {
			return ;
		}
		$this->fail('An expected exception has not been raised.');
	}
	
	public function testCallPropagationStopped() {
		$stopPropagation = true;
		try {
			$op = new \phpstream\operators\PeekOperator(new SquareFunction());
			$op->execute(9, $stopPropagation);
		} catch (LogicException $ex) {
			$this->assertTrue($stopPropagation);
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}
	
}
