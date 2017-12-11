<?php

use phpstream\util\Optional;

class OptionalTest extends \PHPUnit\Framework\TestCase {
	
	public function testNotEmpty() {
		$opt = Optional::of([0, 1, 2]);
		
		$this->assertFalse($opt->isEmpty());
		$this->assertEquals([0, 1, 2], $opt->get());		
	}
	
	public function testEmptyOptional() {
		$opt = Optional::absent();
		
		$this->assertTrue($opt->isEmpty());
		
		try {
			$opt->get();
		} catch (\BadMethodCallException $ex) {
			return ;
		}
		$this->fail('An expected exception has not been raised.');
	}
	
	public function testEmptyOrNot() {
		$opt = Optional::fromNullable([0, 1, 2]);
		$this->assertFalse($opt->isEmpty());
		$this->assertEquals([0, 1, 2], $opt->get());		
		
		$opt = Optional::fromNullable(null);
		$this->assertTrue($opt->isEmpty());
		
		try {
			$opt->get();
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\BadMethodCallException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
	
	public function testInvalidValue() {
		try {
			Optional::of(null);
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
	
	public function testOrElseNullNotEmpty() {
		$opt = Optional::of([0, 1, 2]);
		
		$this->assertEquals([0, 1, 2], $opt->orElse([2, 3, 4]));
		$this->assertEquals([0, 1, 2], $opt->orNull());
		
		try {
			$opt->orElse(null);
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
	
	public function testOrElseNullEmpty() {
		$opt = Optional::absent();
		
		$this->assertEquals([2, 3, 4], $opt->orElse([2, 3, 4]));
		$this->assertNull($opt->orNull());
		
		try {
			$opt->orElse(null);
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
	
	public function testOptionalEquals() {
		$opt = Optional::absent();
		$this->assertTrue($opt->equals($opt));
		$this->assertTrue($opt->equals(Optional::absent()));
		$this->assertFalse($opt->equals(Optional::of([0, 1, 2])));
		$this->assertFalse($opt->equals([0, 1, 2]));
		
		$opt = Optional::of([0, 1, 2]);
		$this->assertTrue($opt->equals($opt));
		$this->assertTrue($opt->equals(Optional::of([0, 1, 2])));
		$this->assertFalse($opt->equals(Optional::absent()));
		$this->assertFalse($opt->equals(Optional::of([2, 3, 4])));
		$this->assertFalse($opt->equals([0, 1, 2]));
	}
	
}
