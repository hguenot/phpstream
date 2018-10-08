<?php
use phpstream\Stream;

class StreamBasicTest extends \PHPUnit\Framework\TestCase {

	public function testCountSimple() {
		$res = Stream::of([1, 2, 3, 4, 5, 6])->count();
		$this->assertEquals(6, $res);
	}

	public function testCountFilter() {
		$res = Stream::of([1, 2, 3, 4, 5, 6])->filter(function ($value) {
			return intval($value) % 2 == 0;
		})
			->count();

		$this->assertEquals(3, $res);
	}

	public function testToArray() {
		$res = Stream::of(['a' => 1, 'z' => 2, 'e' => 3, 'r' => 4, 't' => 5, 'y' => 6])->toArray();
		$this->assertEquals([1, 2, 3, 4, 5, 6], $res);
	}

	public function testToMap() {
		$res = Stream::of(['a' => 1, 'z' => 2, 'e' => 3, 'r' => 4, 't' => 5, 'y' => 6])->toMap();
		$this->assertEquals(['a' => 1, 'z' => 2, 'e' => 3, 'r' => 4, 't' => 5, 'y' => 6], $res);
	}

	public function testConcat() {
		$res = Stream::concat(['a' => 1, 'z' => 2, 'e' => 3], Stream::of(['r' => 4, 't' => 5, 'y' => 6]))->toArray();
		$this->assertEquals([1, 2, 3, 4, 5, 6], $res);
	}

	public function testConcatFail() {
		try {
			Stream::concat(['a' => 1, 'z' => 2, 'e' => 3], 3, Stream::of(['r' => 4, 't' => 5, 'y' => 6]))->toArray();
		} catch (\Exception $ex) {
			$this->assertInstanceOf(\InvalidArgumentException::class, $ex, 'Should be an InvalidArgumentException exception');
		}
	}
}
