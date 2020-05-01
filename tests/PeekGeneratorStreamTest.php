<?php

include_once(__DIR__ . '/PeekMemoryStreamTest.php');

class PeekGeneratorStreamTest extends PeekMemoryStreamTest {
	public function isMemory(): bool {
		return false;
	}
}