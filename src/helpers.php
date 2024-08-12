<?php
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

if (! function_exists('now')) {
    /**
     * Create a new Carbon instance for the current time.
     *
     * @param DateTimeZone|string|null $tz
     * @return Carbon
     */
    function now(DateTimeZone|string|null $tz = null): Carbon
    {
        return Date::now($tz);
    }
}