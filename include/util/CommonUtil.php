<?php

class CommonUtil {

    public static function arrayToReferences($arr)
    {
        $refs = array();

        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];

        return $refs;
    }

    public static function sqlTimeStamp($timestamp)
    {
        return date("Y-m-d H:i:s", $timestamp);
    }
} 