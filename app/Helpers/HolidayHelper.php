<?php

namespace App\Helpers;

use Carbon\Carbon;

class HolidayHelper
{
    public static function getPublicHolidays($year)
    {
        return [
            Carbon::create($year, 1, 1),
            Carbon::create($year, 5, 1),
            Carbon::create($year, 10, 3),
            Carbon::create($year, 12, 25),
            Carbon::create($year, 12, 26),
            self::easterSunday($year)->addDays(1),   // Ostermontag
            self::easterSunday($year)->subDays(2),   // Karfreitag
            self::easterSunday($year)->addDays(39),  // Christi Himmelfahrt
            self::easterSunday($year)->addDays(50),  // Pfingstmontag
            self::easterSunday($year)->addDays(60),  // Fronleichnam
            Carbon::create($year, 11, 1),            // Allerheiligen
        ];
    }

    public static function easterSunday($year)
    {
        return Carbon::create($year, 3, 21)->addDays(easter_days($year));
    }
}
