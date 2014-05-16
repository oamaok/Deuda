<?php

interface IDbRecord {
    public function save();
    public function delete();
    public static function find($criteria = array());
} 