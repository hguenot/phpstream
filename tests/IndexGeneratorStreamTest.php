<?php

include_once(__DIR__ . '/IndexMemoryStreamTest.php');

class IndexGeneratorStreamTest extends IndexMemoryStreamTest {
	public function isMemory(): bool {
		return false;
	}
}