<?php

use phpstream\Stream;

include_once(__DIR__ . '/functions/MinFunction.php');

class StreamReduceTest extends \PHPUnit\Framework\TestCase {

	public function testCallable() {
		$array = [ 2, 3, -2, 4, 5, 6 ];
		$stream = new Stream($array);

		$res = $stream->reduce(function($first, $second){
			return min([ intval($first), intval($second) ]);
		});
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(-2, $res->get());
	}
	
	public function testCallableWithDefault() {
		$array = [ 2, 3, -2, 4, 5, 6 ];
		$stream = new Stream($array);

		$res = $stream->reduceWithDefault(function($first, $second){
			return min([ intval($first), intval($second) ]);
		}, phpstream\util\Optional::of(-23));
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(-2, $res->get());
	}
	
	public function testCallableEmpty() {
		$array = [ ];
		$stream = new Stream($array);

		$res = $stream->reduce(function($first, $second){
			return min([ intval($first), intval($second) ]);
		});
		
		$this->assertTrue($res->isEmpty());
	}
	
	public function testCallableEmptyWithDefault() {
		$array = [ ];
		$stream = new Stream($array);

		$res = $stream->reduceWithDefault(function($first, $second){
			return min([ intval($first), intval($second) ]);
		}, -23);
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(-23, $res->get());
	}
	
	public function testFunction() {
		$array = [ 1, 2, 3, -4, 5, 6 ];
		$stream = new Stream($array);
		$stream->filter(new EvenFunction());
		
		$res = $stream->reduce(new MinFunction());
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(-4, $res->get());
	}
	
	public function testFunctionWithDefault() {
		$array = [ 1, 2, 3, -4, 5, 6 ];
		$stream = new Stream($array);
		$stream->filter(new EvenFunction());
		
		$res = $stream->reduceWithDefault(new MinFunction(), -25);
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(-4, $res->get());
	}
	
	public function testFunctionEmpty() {
		$array = [ ];
		$stream = new Stream($array);
		$res = $stream->reduce(new MinFunction());
		
		$this->assertTrue($res->isEmpty());
	}
	
	public function testFunctionEmptyWithDefault() {
		$array = [ ];
		$stream = new Stream($array);
		$res = $stream->reduceWithDefault(new MinFunction(), phpstream\util\Optional::of(-25));
		
		$this->assertFalse($res->isEmpty());
		$this->assertEquals(-25, $res->get());
	}
	
	public function testException() {
		try {
			$array = [ ];
			$stream = new Stream($array);
			$stream->reduce('MinFunction');
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
	
	
	public function testExceptionWithDefault() {
		try {
			$array = [ ];
			$stream = new Stream($array);
			$stream->reduceWithDefault('MinFunction', phpstream\util\Optional::of(-25));
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
	
}
