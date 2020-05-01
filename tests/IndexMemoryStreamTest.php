<?php

use phpstream\collectors\MapCollector;
use phpstream\Stream;
use PHPUnit\Framework\TestCase;

include_once(__DIR__ . '/bean/Bean.php');
include_once(__DIR__ . '/functions/SquareFunction.php');

class IndexMemoryStreamTest extends TestCase {
	public function isMemory(): bool {
		return true;
	}

	public function testCallable() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory())
		                ->index(function ($value) {
			                return intval($value) * 2;
		                });

		$res = $stream->collect(new MapCollector());

		$this->assertEquals([2 => 1, 4 => 2, 6 => 3, 8 => 4, 10 => 5, 12 => 6], $res);
	}

	public function testProperty() {
		$array = [
				'a' => new Bean(1),
				'b' => new Bean(2),
				'c' => new Bean(3),
				'd' => new Bean(4),
				'e' => new Bean(5),
				'f' => new Bean(6)];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->index('x')
		              ->map('getY')
		              ->toMap();

		$this->assertEquals([1 => 2, 2 => 4, 3 => 6, 4 => 8, 5 => 10, 6 => 12], $res);
	}

	public function testDuplicates() {
		$array = ['a' => new Bean(1), 'b' => new Bean(1)];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->index('x', true)
		              ->map('getY')
		              ->toMap();

		$this->assertEquals([1 => 2], $res);
	}

	public function testDuplicatesFails() {
		$array = ['a' => new Bean(1), 'b' => new Bean(1)];
		$stream = Stream::of($array, $this->isMemory());
		try {
			$stream->index('x', false)
			       ->map('getY')
			       ->toMap();
			$this->fail('An expected exception has not been raised.');
		} catch (Exception $ex) {
			$this->assertInstanceOf(InvalidArgumentException::class, $ex,
					'Should be an InvalidArgumentException exception');
		}
	}
}
