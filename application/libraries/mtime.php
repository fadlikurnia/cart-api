<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This class contain of all support fuction for development
 * @author : Parama_Fadli_Kurnia
 * Developer can make some additional code in this classs
 */
class Mtime {
    /* validate gender by user input */

    // get date format now
    function now() {
        $data = array();
        $today = date("Y-m-d H:i:s", time());
        $time_array = explode(" ", $today);
        $date = $time_array[0];
        $date_array = explode("-", $date);

        $data["d_day"] = implode("", $date_array);
        $data["d_month"] = $date_array[0] . $date_array[1];
        $data["d_year"] = $date_array[0];
        $data["created_date"] = $today;
        $data["d_date"] = $date;
        return $data;
    }

    // get all date between two date
    function date_between($date_init, $date_end) {
        $aryRange = array();
        $iDateFrom = mktime(1, 0, 0, substr($date_init, 5, 2), substr($date_init, 8, 2), substr($date_init, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($date_end, 5, 2), substr($date_end, 8, 2), substr($date_end, 0, 4));
        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }

    // get yesterday
    function previous_date($date) {
        return date('Y-m-d', strtotime($date . ' -1 day'));
    }

    // get tommorow
    function next_date($date) {
        return date('Y-m-d', strtotime($date . ' +1 day'));
    }

    // get date by user
    function prev_next_date($date, $range) {
        return date('Y-m-d', strtotime($date . " $range day"));
    }

    // parse date into day-month-year format
    function get_component_date($date) {
        $date_array = array();
        $date_array = explode("-", $date);
        $data = array();
        $data["day"] = $date_array[2];
        $data["month"] = $date_array[1];
        $data["year"] = $date_array[0];
        return $data;
    }

    // compare between two dates
    function still_prev_day($init_date, $end_date) {
        $date1=date_create($init_date);
        $date2=date_create($end_date);
        $diff = date_diff($date1, $date2);
        $res = $diff->format("%R%a");
        if ($res >= 0) {
//            echo "$res KD";
            return TRUE;
        } else {
//            echo "$res LD";
            return FALSE;
        }
    }

    // convert datetime to timestamp
    function to_timestamp($date){
        return strtotime($date.' 00:00:00');
    }
    
    // convert timestamp to datetime
    function to_datetime($date){
        return date('Y-m-d H:i:s', $date);
    }
    
}

?>