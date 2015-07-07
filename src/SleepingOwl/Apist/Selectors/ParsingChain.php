<?php namespace SleepingOwl\Apist\Selectors;

use InvalidArgumentException;
use SleepingOwl\Apist\Blueprint;
use SleepingOwl\Apist\SuppressExceptionTrait;
use Symfony\Component\DomCrawler\Crawler;

class ParsingChain
{
    use SuppressExceptionTrait;
    /**
     * @var string
     */
    protected $selector;
    /**
     * @var ResultCallback[]
     */
    protected $resultMethodChain = [];

    /**
     * @param $selector
     */
    public function __construct($selector)
    {
        $this->selector = $selector;
        $this->resultMethodChain = new ResultCallbackChain();
    }

    /**
     * Get value from content by css selector
     *
     * @param Crawler $rootNode
     * @param \SleepingOwl\Apist\Blueprint $parser
     *
     * @return array|null|string|\Symfony\Component\DomCrawler\Crawler
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function getValue(Crawler $rootNode, Blueprint $parser)
    {
        $node = $rootNode->filter($this->selector);

        return $this->resultMethodChain->call($node, $parser);
    }

    /**
     * Save callable method as result callback to perform it after getValue method
     *
     * @param $name
     * @param $arguments
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        return $this->addCallback($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return $this
     */
    public function addCallback($name, $arguments = [])
    {
        $this->resultMethodChain->addCallback($name, $arguments);

        return $this;
    }
}