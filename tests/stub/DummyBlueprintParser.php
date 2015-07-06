<?php

/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 06.07.15
 * Time: 18:08
 */
class DummyBlueprintParser extends \SleepingOwl\Apist\BlueprintParser
{
    public function __construct()
    {
    }

    public function parse($blueprint, $node = null)
    {
        return null;
    }

}