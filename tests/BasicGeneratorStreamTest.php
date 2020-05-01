<?php

include_once(__DIR__ . '/BasicMemoryStreamTest.php');

class BasicGeneratorStreamTest extends BasicMemoryStreamTest {
	public function isMemory(): bool {
		return false;
	}
}