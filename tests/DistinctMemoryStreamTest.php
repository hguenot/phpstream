<?php
use phpstream\collectors\ArrayCollector;
use phpstream\collectors\MapCollector;
use phpstream\Stream;
use PHPUnit\Framework\TestCase;

class DistinctMemoryStreamTest extends TestCase {

	public function isMemory(): bool {
		return true;
	}

	public function testList() {
		$array = [1, 2, 2, 3, 4, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->distinct()
			->collect(new ArrayCollector());

		$this->assertEquals([1, 2, 3, 4, 5, 6], $res);
	}

	public function testMap() {
		$array = ['a' => 1, 'b' => 2, 'c' => 2, 'd' => 3, 'e' => 4, 'f' => 4, 'g' => 5, 'h' => 6];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->distinct()
			->collect(new MapCollector());

		$this->assertEquals(['a' => 1, 'b' => 2, 'd' => 3, 'e' => 4, 'g' => 5, 'h' => 6], $res);
	}
}
