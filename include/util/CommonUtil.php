<?php

class CommonUtil {

    public static function arrayToReferences($arr)
    {
        $refs = array();

        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];

        return $refs;
    }

    /**
     * @param $timestamp int
     * @return string
     *
     * Converts UNIX timestamp into a format accepted by MySQL.
     */
    public static function sqlTimeStamp($timestamp)
    {
        return date("Y-m-d H:i:s", $timestamp);
    }
} 