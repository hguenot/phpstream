<?php

use phpstream\functions\UnaryFunction;
use phpstream\util\Optional;
use PHPUnit\Framework\AssertionFailedError;

class OptionalTest extends \PHPUnit\Framework\TestCase {

	public function testNotEmpty() {
		$opt = Optional::of([0, 1, 2]);

		$this->assertTrue($opt->isNotEmpty());
		$this->assertFalse($opt->isEmpty());
		$this->assertEquals([0, 1, 2], $opt->get());
	}

	public function testEmptyOptional() {
		$opt = Optional::absent();

		$this->assertTrue($opt->isEmpty());

		try {
			$opt->get();
			$this->fail('An expected exception has not been raised.');
		} catch (AssertionFailedError $ex) {
			throw $ex;
		} catch (Exception $ex) {
			$this->assertInstanceOf(BadMethodCallException::class, $ex, 'Should be an InvalidArgumentException exception '.get_class($ex));
		}
	}

	public function testEmptyOrNot() {
		$opt = Optional::ofNullable([0, 1, 2]);
		$this->assertFalse($opt->isEmpty());
		$this->assertEquals([0, 1, 2], $opt->get());

		$opt->map(function ($array) {
			return max($array);
		});

		$opt = Optional::ofNullable(null);
		$this->assertTrue($opt->isEmpty());

		try {
			$opt->get();
			$this->fail('An expected exception has not been raised.');
		} catch (AssertionFailedError $ex) {
			throw $ex;
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\BadMethodCallException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}

	public function testMap() {
		$fn = function ($array) {
			return max($array);
		};
		
		$opt = Optional::of([0, 1, 2])->map($fn);
		$this->assertEquals(2, $opt->get());
		
		$opt = Optional::ofNullable(null)->map($fn);
		$this->assertEquals(true, $opt->isEmpty());

		$opt = Optional::of([0, 1, 2])->map(new class implements UnaryFunction {
			public function apply(mixed $array): mixed {
				return max($array);
			}
		});
		$this->assertEquals(2, $opt->get());

	}

	public function testInvalidValue() {
		try {
			Optional::of(null);
			$this->fail('An expected exception has not been raised.');
		} catch (AssertionFailedError $ex) {
			throw $ex;
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
		} catch (AssertionFailedError $ex) {
			throw $ex;
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
		} catch (AssertionFailedError $ex) {
			throw $ex;
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
		$this->assertEquals($opt, Optional::of($opt));
		
		$opt = Optional::of([0, 1, 2]);
		$this->assertTrue($opt->equals($opt));
		$this->assertTrue($opt->equals(Optional::of([0, 1, 2])));
		$this->assertFalse($opt->equals(Optional::absent()));
		$this->assertFalse($opt->equals(Optional::of([2, 3, 4])));
		$this->assertFalse($opt->equals([0, 1, 2]));
		$this->assertEquals($opt, Optional::ofNullable($opt));
	}
	
	public function testOptionalThrows() {
		try {
			Optional::of(2)->orElseThrow(new \InvalidArgumentException('yeah'));
		} catch (\Exception $ex) {
			$this->fail('An expected exception has been raised.');
		}
		
		try {
			Optional::absent()->orElseThrow(new \InvalidArgumentException('yeah'));
			$this->fail('An expected exception has not been raised.');
		} catch (AssertionFailedError $ex) {
			throw $ex;
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
}
