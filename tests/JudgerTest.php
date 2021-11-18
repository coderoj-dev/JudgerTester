<?php

use PHPUnit\Framework\TestCase;
use JudgerTest\Generator\TestGenerator;

final class JudgerTest extends TestCase
{
	
    public function testJudgerTestingData1(): void
    {
		$test = (new TestGenerator(1))->check();
        $this->assertTrue($test->assert, $test->assertError);
    }

    public function testJudgerTestingData2(): void
    {
		$test = (new TestGenerator(2))->check();
        $this->assertTrue($test->assert, $test->assertError);
    }
}
