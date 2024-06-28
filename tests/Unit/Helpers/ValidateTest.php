<?php

namespace Tests\Unit\Helpers;

use App\Helpers\HelperValidate;
use PHPUnit\Framework\TestCase;

class ValidateTest extends TestCase
{
    /**
     * @dataProvider jsonProvider
     */
    public function testIsJson($input, $expected)
    {
        $this->assertEquals($expected, HelperValidate::isJson($input));
    }

    public static function jsonProvider()
    {
        return [
            ['{"a":1,"b":2,"c":3,"d":4,"e":5}', true],   // Test valid JSON
            ['{"a":1,"b":2,"c":3,"d":4,"e":5', false],   // Test missing closing bracket
            ['Just a regular string.', false],           // Test non-JSON string
            ['', false],                                 // Test empty string
            [null, false],                               // Test null string
        ];
    }
}
