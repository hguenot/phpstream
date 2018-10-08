<?php
use phpstream\collectors\ListCollector;
use phpstream\collectors\MapCollector;
use phpstream\Stream;
use phpstream\operators\MapOperator;
use phpstream\functions\UnaryFunction;

include_once (__DIR__ . '/bean/Bean.php');
include_once (__DIR__ . '/functions/SquareFunction.php');

class StreamMapTest extends \PHPUnit\Framework\TestCase {

	public function testCallable() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = new Stream($array);
		$stream->map(function ($value) {
			return intval($value) * intval($value);
		});

		$res = $stream->collect(new ListCollector());

		$this->assertEquals([1, 4, 9, 16, 25, 36], $res);
	}

	public function testFunction() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = new Stream($array);
		$stream->map(new SquareFunction());

		$res = $stream->collect(new ListCollector());

		$this->assertEquals([1, 4, 9, 16, 25, 36], $res);
	}

	public function testMapCollector() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = new Stream($array);
		$stream->map(new SquareFunction());

		$res = $stream->collect(new MapCollector());

		$this->assertEquals(['a' => 1, 'b' => 4, 'c' => 9, 'd' => 16, 'e' => 25, 'f' => 36], $res);
	}

	public function testMapCollectorValue() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = new Stream($array);
		$res = $stream->collect(new MapCollector(function ($key, $value) {
			return $value;
		}, function ($key, $value) {
			return strtoupper($key);
		}));

		$this->assertEquals([1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F'], $res);
	}

	public function testNotCallable() {
		try {
			Stream::of()->map(true);
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}

	public function testCallPropagationStopped() {
		$stopPropagation = true;
		try {
			$op = new \phpstream\operators\MapOperator(new SquareFunction());
			$op->execute(9, $stopPropagation);
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(LogicException::class, $ex, 'Should be an LogicException exception');
			$this->assertTrue($stopPropagation);
		}
	}

	public function testPropertyMap() {
		$array = ['a' => new \Bean(1), 'b' => new \Bean(2), 'c' => new \Bean(3), 'd' => new \Bean(4), 'e' => new \Bean(5), 'f' => new \Bean(6)];
		$stream = new Stream($array);
		$res = $stream->map('x')
			->toMap();

		$this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6], $res);
	}

	public function testMethodMap() {
		$array = ['a' => new \Bean(1), 'b' => new \Bean(2), 'c' => new \Bean(3), 'd' => new \Bean(4), 'e' => new \Bean(5), 'f' => new \Bean(6)];
		$stream = new Stream($array);
		$res = $stream->map('getY')
			->toMap();

		$this->assertEquals(['a' => 2, 'b' => 4, 'c' => 6, 'd' => 8, 'e' => 10, 'f' => 12], $res);
	}

	public function testMapOperatorParameter() {
		$array = ['a' => new \Bean(1), 'b' => new \Bean(2), 'c' => new \Bean(3), 'd' => new \Bean(4), 'e' => new \Bean(5), 'f' => new \Bean(6)];
		$stream = new Stream($array);
		$res = $stream->map(new MapOperator(new class() implements UnaryFunction {

			public function apply($value) {
				return $value->x * 2;
			}
		}))
			->toMap();

		$this->assertEquals(['a' => 2, 'b' => 4, 'c' => 6, 'd' => 8, 'e' => 10, 'f' => 12], $res);
	}

	public function testNonObjProperty() {
		try {
			$array = ['a' => new \Bean(1), 'b' => new \Bean(2), 'c' => new \Bean(3), 'd' => new \Bean(4), 'e' => new \Bean(5), 'f' => new \Bean(6)];
			$stream = new Stream($array);
			$stream->map('getX')
				->toMap();
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
}
