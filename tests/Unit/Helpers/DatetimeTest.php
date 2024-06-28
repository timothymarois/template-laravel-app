<?php

namespace Tests\Unit\Helpers;

use App\Helpers\HelperTime;
use PHPUnit\Framework\TestCase;

class DatetimeTest extends TestCase
{
    public function test_convert_timezone()
    {
        $this->assertEquals('2020-03-11 20:00:00', HelperTime::convertTz('2020-03-12 06:00:00', 'Europe/Brussels', 'Australia/Melbourne')
            ->toDateTimeString());

        $this->assertEquals('2020-03-11 09:45:00', HelperTime::convertTz('2020-03-11 19:15:00', 'America/New_York', 'Asia/Kolkata')
            ->toDateTimeString());

        $this->assertEquals('2020-03-11 09:45:00', HelperTime::convertTz('2020-03-11 13:45:00', 'America/New_York')
            ->toDateTimeString());
    }

    /**
     * @dataProvider timestampsProvider
     */
    public function test_calc_total_seconds($timestamps, $expected)
    {
        $this->assertEquals($expected, HelperTime::calcTotalSeconds($timestamps));
    }

    public static function timestampsProvider()
    {
        return [
            [
                ['2023-12-25 18:00:00', '2023-12-25 17:00:00', '2023-12-25 16:00:00'], // Test with 3 timestamps
                7200, // 2 hours difference in total
            ],
            [
                ['2023-12-25 16:00:00', '2023-12-25 18:00:00', '2023-12-25 17:00:00'], // Test with 3 timestamps, incorrect order
                7200, // 2 hours difference in total
            ],
            [
                ['2023-12-25 17:00:00', '2023-12-25 16:30:00'], // Test with 2 timestamps
                1800, // 0.5 hour difference
            ],
            [
                ['2023-12-25 17:00:00'], // Test with single timestamp
                0, // No difference
            ],
            [
                [], // Test with empty array
                0, // No difference
            ],
        ];
    }

    /**
     * @dataProvider dateProvider
     */
    public function test_dates_between($dateFrom, $dateTo, $expected)
    {
        $result = HelperTime::datesBetween($dateFrom, $dateTo);

        if (is_array($result)) {
            $result = array_map(function ($date) {
                return $date->format('Y-m-d');
            }, $result);
        } else {
            $result = iterator_to_array($result);
            $result = array_map(function ($date) {
                return $date->format('Y-m-d');
            }, $result);
        }

        $this->assertEquals($expected, $result);
    }

    public static function dateProvider()
    {
        return [
            ['2023-12-25', '2023-12-25', ['2023-12-25']], // Test same dates
            ['2023-12-25', '2023-12-27', ['2023-12-25', '2023-12-26', '2023-12-27']], // Test multiple days
            ['2023-12-25', null, ['2023-12-25']], // Test null dateTo
        ];
    }
}
