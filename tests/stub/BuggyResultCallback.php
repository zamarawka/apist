<?php

/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 06.07.15
 * Time: 18:05
 */
class BuggyResultCallback extends \SleepingOwl\Apist\Selectors\ResultCallback
{
    public function __construct()
    {
    }

    public function apply($node, \SleepingOwl\Apist\Blueprint $parser)
    {
        throw new \Exception();
    }

}