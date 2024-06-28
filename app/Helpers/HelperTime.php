<?php

namespace App\Helpers;

use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;

class HelperTime
{
    /**
     * Convert a given date/time to a date/time of a different timezone
     *
     * @param string $datetime
     * @param string $useTimezone
     * @param string #defaultTimezone
     * @return Carbon
     */
    public static function convertTz(string $datetime, string $useTimezone, string $defaultTimezone = 'UTC'): Carbon
    {
        // return Carbon::parse($datetime.' '.$defaultTimezone)->tz($useTimezone);
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $datetime, $defaultTimezone);
        return $date->setTimezone($useTimezone);
    }

    /**
     * Get the total seconds from an array of timestamps
     *
     * @param Array $timestamps
     * @return int
     */
    public static function calcTotalSeconds(array $timestamps = []): int
    {
        rsort($timestamps);
        $seconds = 0;
        for ($i = 1; $i < count($timestamps); $i++) {
            $d1 = new DateTime($timestamps[$i]);
            $d2 = new DateTime($timestamps[$i - 1]);
            $seconds += ($d2->getTimestamp() - $d1->getTimestamp());
        }

        return $seconds;
    }

    /**
     * Get an array of dates between a date range
     *
     * @param string $startDate
     * @param ?string $endDate
     */
    public static function datesBetween(string $startDate, ?string $endDate)
    {
        if ($startDate == $endDate || ! $endDate) {
            return [new DateTime($startDate)];
        } else {
            return new DatePeriod(
                new DateTime($startDate),
                new DateInterval('P1D'),
                (new DateTime($endDate))->modify('+1 day')
            );
        }
    }
}
