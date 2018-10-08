<?php
use phpstream\collectors\ListCollector;
use phpstream\collectors\MapCollector;
use phpstream\Stream;
use phpstream\operators\FilterOperator;
use phpstream\functions\UnaryFunction;

include_once (__DIR__ . '/functions/EvenFunction.php');

class StreamFilterTest extends \PHPUnit\Framework\TestCase {

	public function testCallable() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = new Stream($array);
		$stream->filter(function ($value) {
			return intval($value) % 2 == 0;
		});

		$res = $stream->collect(new ListCollector());

		$this->assertEquals([2, 4, 6], $res);
	}

	public function testFunction() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = new Stream($array);
		$stream->filter(new EvenFunction());

		$res = $stream->collect(new ListCollector());

		$this->assertEquals([2, 4, 6], $res);
	}

	public function testFilterOperator() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = new Stream($array);
		$stream->filter(new FilterOperator(new class() implements UnaryFunction {

			public function apply($value) {
				return intval($value) % 2 == 0;
			}
		}));

		$res = $stream->collect(new ListCollector());

		$this->assertEquals([2, 4, 6], $res);
	}

	public function testMapCollector() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = new Stream($array);
		$stream->filter(function ($value) {
			return intval($value) % 2 == 0;
		});

		$res = $stream->collect(new MapCollector());

		$this->assertEquals(['b' => 2, 'd' => 4, 'f' => 6], $res);
	}

	public function testMapCollectorKeyFn1() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = new Stream($array);
		$stream->filter(function ($value) {
			return intval($value) % 2 == 0;
		});

		$res = $stream->collect(new MapCollector(function ($key, $value) {
			return 'z_' . $key;
		}));

		$this->assertEquals(['z_b' => 2, 'z_d' => 4, 'z_f' => 6], $res);
	}

	public function testMapCollectorKeyFn2() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = new Stream($array);
		$stream->filter(function ($value) {
			return intval($value) % 2 == 0;
		});

		$res = $stream->collect(new MapCollector(function ($key, $value) {
			return $value * 2;
		}));

		$this->assertEquals([4 => 2, 8 => 4, 12 => 6], $res);
	}

	public function testNotCallable() {
		try {
			Stream::of()->filter(true);
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}

	public function testCallPropagationStopped() {
		$stopPropagation = true;
		try {
			$op = new \phpstream\operators\FilterOperator(new EvenFunction());
			$op->execute(9, $stopPropagation);
			$this->fail('An expected exception has not been raised.');
		} catch (LogicException $ex) {
			$this->assertInstanceOf(\LogicException::class, $ex, 'Should be an LogicException exception');
			$this->assertTrue($stopPropagation);
		}
	}
}
