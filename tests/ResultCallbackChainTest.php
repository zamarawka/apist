<?php
/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 06.07.15
 * Time: 17:57
 */


class ResultCallbackChainTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_throw_exception()
    {
        $chain = new \SleepingOwl\Apist\Selectors\ResultCallbackChain();
        $callback = new BuggyResultCallback();
        $parser = new DummyBlueprintParser();

        $chain->append($callback);

        $crawler = $this->getMockBuilder(\Symfony\Component\DomCrawler\Crawler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectedException(\Exception::class);
        $chain->call($crawler, $parser);
    }
}
