<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;
use Carbon\Carbon;

class ServeCommand extends BaseServeCommand
{
    /**
     * Get the date from the given PHP server output line.
     *
     * @param  string  $line
     * @return \Carbon\Carbon
     */
    protected function getDateFromLine($line)
    {
        $regex = '/^\[([^\]]+)\]/';

        $line = str_replace('  ', ' ', $line);

        // Set locale to C to ensure English date format
        $originalLocale = setlocale(LC_TIME, 0);
        setlocale(LC_TIME, 'C');

        preg_match($regex, $line, $matches);

        // Restore original locale
        setlocale(LC_TIME, $originalLocale);

        return Carbon::createFromFormat('D M d H:i:s Y', $matches[1]);
    }
}