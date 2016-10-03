<?php namespace Znck\Transformers;

abstract class Transformer
{
    public static $transformers = [];

    public static function register(array $map) {
        self::$transformers += $map;
    }
}
