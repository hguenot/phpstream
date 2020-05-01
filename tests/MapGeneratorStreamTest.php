<?php

include_once(__DIR__ . '/MapMemoryStreamTest.php');

class MapGeneratorStreamTest extends MapMemoryStreamTest {
	public function isMemory(): bool {
		return false;
	}
}