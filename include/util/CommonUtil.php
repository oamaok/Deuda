<?php

class CommonUtil {

    public static function arrayToReferences($arr)
    {
        $refs = array();

        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];

        return $refs;
    }
} 