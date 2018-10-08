<?php
use phpstream\collectors\ListCollector;
use phpstream\collectors\MapCollector;
use phpstream\Stream;
use phpstream\operators\PeekOperator;
use phpstream\functions\UnaryFunction;

include_once (__DIR__ . '/functions/SquareFunction.php');

class StreamPeekTest extends \PHPUnit\Framework\TestCase {

	public function testCallable() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = new Stream($array);
		$stream->peek(function ($value) {
			return intval($value) * intval($value);
		});

		$res = $stream->collect(new ListCollector());

		$this->assertEquals([1, 2, 3, 4, 5, 6], $res);
	}

	public function testFunction() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = new Stream($array);
		$stream->peek(new SquareFunction());

		$res = $stream->collect(new ListCollector());

		$this->assertEquals([1, 2, 3, 4, 5, 6], $res);
	}

	public function testMapCollector() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = new Stream($array);
		$stream->peek(new SquareFunction());

		$res = $stream->collect(new MapCollector());

		$this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6], $res);
	}

	public function testPropertyMap() {
		$array = ['a' => new \Bean(1), 'b' => new \Bean(2), 'c' => new \Bean(3), 'd' => new \Bean(4), 'e' => new \Bean(5), 'f' => new \Bean(6)];
		$stream = new Stream($array);
		$res = $stream->peek('x')
			->toMap();

		$this->assertEquals($array, $res);
	}

	public function testMethodMap() {
		$array = ['a' => new \Bean(1), 'b' => new \Bean(2), 'c' => new \Bean(3), 'd' => new \Bean(4), 'e' => new \Bean(5), 'f' => new \Bean(6)];
		$stream = new Stream($array);
		$res = $stream->peek('getY')
			->toMap();

		$this->assertEquals($array, $res);
	}

	public function testMapOperatorParameter() {
		$array = ['a' => new \Bean(1), 'b' => new \Bean(2), 'c' => new \Bean(3), 'd' => new \Bean(4), 'e' => new \Bean(5), 'f' => new \Bean(6)];
		$stream = new Stream($array);
		$res = $stream->peek(new PeekOperator(new class() implements UnaryFunction {

			public function apply($value) {
				$value->x *= 2;
			}
		}))
			->toMap();

		$this->assertEquals(['a' => new \Bean(2), 'b' => new \Bean(4), 'c' => new \Bean(6), 'd' => new \Bean(8), 'e' => new \Bean(10), 'f' => new \Bean(12)], $res);
	}

	public function testNotCallable() {
		try {
			Stream::of()->peek(true);
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
			return;
		}
	}

	public function testCallPropagationStopped() {
		$stopPropagation = true;
		try {
			$op = new \phpstream\operators\PeekOperator(new SquareFunction());
			$op->execute(9, $stopPropagation);
			$this->fail('An expected exception has not been raised.');
		} catch (\Exception $ex) {
			$this->assertInstanceOf(LogicException::class, $ex, 'Should be an InvalidArgumentException exception');
			$this->assertTrue($stopPropagation);
		}
	}

	public function testNonObjProperty() {
		try {
			$array = ['a' => new \Bean(1), 'b' => new \Bean(2), 'c' => new \Bean(3), 'd' => new \Bean(4), 'e' => new \Bean(5), 'f' => new \Bean(6)];
			$stream = new Stream($array);
			$stream->peek('getX')
				->toMap();
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
}
