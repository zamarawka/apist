<?php

/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 07.07.15
 * Time: 10:25
 */
class ApplyToArrayTest extends PHPUnit_Framework_TestCase
{
    public function testShouldParseNodeArray()
    {
        $confParser = new \SleepingOwl\Apist\BlueprintConfigParser();
        $confParser->parse([
            'index' => [
                'blueprint' => [
                    'name' => 'li | text'
                ]
            ]
        ]);

        $parser = new \SleepingOwl\Apist\Blueprint();

        $node_1 = new \SleepingOwl\Apist\DomCrawler\Crawler('<li>Node 1</li>');
        $node_2 = new \SleepingOwl\Apist\DomCrawler\Crawler('<li>Node 2</li>');
        $callback = new \SleepingOwl\Apist\Selectors\ResultCallback('text', []);
        $result = $callback->apply([$node_1, $node_2], $parser);

        $this->assertEquals(['Node 1', 'Node 2'], $result);
    }
}