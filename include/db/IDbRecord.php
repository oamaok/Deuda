<?php

interface IDbRecord {
    public function save();
    public function delete();
    public function find($criteria = array());
} 