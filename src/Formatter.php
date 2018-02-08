<?php

namespace Phperf\XhTool;


class Formatter
{
    public static function timeFromNs($time)
    {
        if ($time < 1000) {
            return $time . 'us';
        } elseif ($time < 1000000) {
            return round($time/1000, 2) . 'ms';
        } elseif ($time < 60000000) {
            return round($time/1000000, 2) . 's';
        } else {
            return round($time / 1000000, 2) . 's';
        }
    }


    public static function bytes($bytes)
    {
        if ($bytes < 1024) {
            return $bytes;
        }
        elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . 'K';
        }
        else {
            return round($bytes / 1048576, 2) . 'M';
        }
    }

    public static function count($cnt)
    {
        if ($cnt < 1000) {
            return $cnt;
        } elseif ($cnt < 1000000) {
            return round($cnt / 1000) . 'K';
        } else {
            return round($cnt / 1000000) . 'M';
        }
    }


}
