<?php

namespace Tests\Unit\Helpers;

use App\Helpers\HelperNumber;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    /**
     * @dataProvider booleanValues
     */
    public function test_convert_boolean($value, $expected)
    {
        $this->assertEquals($expected, HelperNumber::convertBoolean($value));
    }

    public static function booleanValues(): array
    {
        return [
            ['true', true],
            ['false', false],
            ['1', true],
            ['0', false],
            [1, true],
            [0, false],
            ['TRUE', true],
            ['FALSE', false],
            ['', false],
            [null, false],
        ];
    }

    /**
     * @dataProvider sizeValues
     */
    public function test_convert_size($memorySize, $expected)
    {
        $this->assertEquals($expected, HelperNumber::convertBytesToReadable($memorySize));
    }

    public static function sizeValues(): array
    {
        return [
            [1024, '1kb'],
            [1024 * 2, '2kb'],
            [1024 * 5.3, '5.3kb'],
            [1024 * 1024, '1mb'],
            [1024 * 1024 * 1024, '1gb'],
            [1024 * 1024 * 1024 * 1024, '1tb'],
            [1024 * 1024 * 1024 * 1024 * 1024 * 3, '3pb'],
        ];
    }

    /** @dataProvider ceilValues */
    public function test_nearest_10_ceil($entry, $expectation)
    {
        $this->assertSame($expectation, HelperNumber::nearestTen($entry));
    }

    public static function ceilValues(): array
    {
        return [
            [100, 100],
            [159, 160],
            [155, 160],
            [154, 160],
            [150, 150],
        ];
    }
}
