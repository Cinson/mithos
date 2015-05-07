<?php
    
namespace Mithos\Util;

class Time {

    public function relative($dt, $precision = 2, $after = 'ago') {
        $times = array(
            365 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60 => 'month',
            7 * 24 * 60 * 60 => 'week',
            24 * 60 * 60 => 'day',
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
        $passed = time() - strtotime($dt);
        if ($passed < 5) {
            $output = '5 ' . __('seconds') . ' ' . __($after);
        } else {
            $output = array();
            $exit = 0;
            foreach ($times as $period => $name) {
                if ($exit >= $precision || ($exit > 0 and $period < 60)) {
                    break;
                }
                $result = floor($passed / $period);
                if ($result > 0) {
                    $output[] = $result . ' ' . __($name . ($result == 1 ? '' : 's'));
                    $passed -= $result * $period;
                    $exit++;
                } elseif ($exit > 0) {
                    $exit++;
                }
            }
            $output = implode(' ' . __('and') . ' ', $output) . ' ' . __($after);
        }
        return $output;
    }
}