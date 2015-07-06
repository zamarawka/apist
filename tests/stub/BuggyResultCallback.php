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

    public function apply(\Symfony\Component\DomCrawler\Crawler $node, \SleepingOwl\Apist\BlueprintParser $parser)
    {
        throw new \Exception();
    }

}