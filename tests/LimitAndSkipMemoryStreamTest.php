<?php
use phpstream\collectors\ArrayCollector;
use phpstream\collectors\MapCollector;
use phpstream\Stream;
use PHPUnit\Framework\TestCase;

class LimitAndSkipMemoryStreamTest extends TestCase {

	public function isMemory(): bool {
		return true;
	}

	public function testLimit() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->limit(2);

		$res = $stream->collect(new ArrayCollector());

		$this->assertEquals([1, 2], $res);
	}

	public function testSkip() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->skip(4);

		$res = $stream->collect(new ArrayCollector());

		$this->assertEquals([5, 6], $res);
	}

	public function testLimitMapCollector() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->limit(2);

		$res = $stream->collect(new MapCollector());

		$this->assertEquals(['a' => 1, 'b' => 2], $res);
	}

	public function testSkipMapCollector() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->skip(3);

		$res = $stream->collect(new MapCollector());

		$this->assertEquals(['d' => 4, 'e' => 5, 'f' => 6], $res);
	}

	public function testLimitFail() {
		try {
			$array = [1, 2, 3, 4, 5, 6];
			$stream = Stream::of($array, $this->isMemory());
			$stream->limit(-1);

			$res = $stream->collect(new ArrayCollector());

			$this->assertEquals([1, 2], $res);
		} catch (Exception $ex) {
			$this->assertInstanceOf(InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}

	public function testSkipFail() {
		try {
			$array = [1, 2, 3, 4, 5, 6];
			$stream = Stream::of($array, $this->isMemory());
			$stream->skip(-5);

			$res = $stream->collect(new ArrayCollector());

			$this->assertEquals([5, 6], $res);
		} catch (Exception $ex) {
			$this->assertInstanceOf(InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}

}
