<?php
use phpstream\collectors\ArrayCollector;
use phpstream\collectors\MapCollector;
use phpstream\Stream;
use phpstream\operators\PeekOperator;
use phpstream\functions\UnaryFunction;
use PHPUnit\Framework\TestCase;

include_once (__DIR__ . '/functions/SquareFunction.php');

class PeekMemoryStreamTest extends TestCase {

	public function isMemory(): bool {
		return true;
	}

	public function testCallable() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->peek(function ($value) {
			return intval($value) * intval($value);
		});

		$res = $stream->collect(new ArrayCollector());

		$this->assertEquals([1, 2, 3, 4, 5, 6], $res);
	}

	public function testFunction() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->peek(new SquareFunction());

		$res = $stream->collect(new ArrayCollector());

		$this->assertEquals([1, 2, 3, 4, 5, 6], $res);
	}

	public function testMapCollector() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->peek(new SquareFunction());

		$res = $stream->collect(new MapCollector());

		$this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6], $res);
	}

	public function testMapOperatorParameter() {
		$array = ['a' => new \Bean(1), 'b' => new \Bean(2), 'c' => new \Bean(3), 'd' => new \Bean(4), 'e' => new \Bean(5), 'f' => new \Bean(6)];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->peek(new PeekOperator(new class() implements UnaryFunction {

			public function apply(mixed $value): int {
				$value->x *= 2;
				return $value->x;
			}
		}))
			->toMap();

		$this->assertEquals(['a' => new \Bean(2), 'b' => new \Bean(4), 'c' => new \Bean(6), 'd' => new \Bean(8), 'e' => new \Bean(10), 'f' => new \Bean(12)], $res);
	}
}
