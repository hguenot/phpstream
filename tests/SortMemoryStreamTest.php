<?php
use phpstream\collectors\ArrayCollector;
use phpstream\operators\MapOperator;
use phpstream\Stream;
use PHPUnit\Framework\TestCase;

include_once (__DIR__ . '/comparators/ReverseComparator.php');

class SortMemoryStreamTest extends TestCase {

	public function isMemory(): bool {
		return true;
	}

	public function testSort() {
		$array = ['l', 'e', 'z', 'a'];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->sort()
			->collect(new ArrayCollector());

		$this->assertEquals(['a', 'e', 'l', 'z'], $res);
	}

	public function testCallable() {
		$array = [2, 4, -2, -9];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->sort(function ($first, $second) {
			return (intval($first) - intval($second)) / abs(intval($first) - intval($second));
		})
			->collect(new ArrayCollector());

		$this->assertEquals([-9, -2, 2, 4], $res);
	}

	public function testComparator() {
		$array = [2, 4, -2, -9];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->sort(new ReverseComparator())
			->collect(new ArrayCollector());

		$this->assertEquals([4, 2, -2, -9], $res);
	}

	public function testException() {
		try {
			$array = [2, 4, -2, -9];
			$stream = Stream::of($array, $this->isMemory());
			$stream->sort('reverseComparator')
				->count();
			$this->fail('An expected exception has not been raised.');
		} catch (Exception $ex) {
			$this->assertInstanceOf(InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}

	public function testException2() {
		try {
			$array = [2, 4, -2, -9];
			$stream = Stream::of($array, $this->isMemory());
			$stream->sort(new ArrayCollector())
				->count();
			$this->fail('An expected exception has not been raised.');
		} catch (Exception $ex) {
			$this->assertInstanceOf(InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
}
