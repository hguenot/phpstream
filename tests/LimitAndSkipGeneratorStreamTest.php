<?php

include_once(__DIR__ . '/LimitAndSkipMemoryStreamTest.php');

class LimitAndSkipGeneratorStreamTest extends LimitAndSkipMemoryStreamTest {
	public function isMemory(): bool {
		return false;
	}
}