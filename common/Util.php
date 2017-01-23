<?php
namespace common;

class Util 
{
    public static function dump($data) {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
    }

    public static function UUID() {
        return md5(uniqid());
    }
}