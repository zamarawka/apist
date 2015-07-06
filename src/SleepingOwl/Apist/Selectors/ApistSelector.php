<?php namespace SleepingOwl\Apist\Selectors;

use InvalidArgumentException;
use SleepingOwl\Apist\Methods\ApistMethod;
use SleepingOwl\Apist\SuppressExceptionTrait;
use Symfony\Component\DomCrawler\Crawler;

class ApistSelector
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
     * @param ApistMethod $method
     * @param Crawler $rootNode
     *
     * @return array|null|string|Crawler
     * @throws \InvalidArgumentException
     */
    public function getValue(ApistMethod $method, Crawler $rootNode = null)
    {
        if ($rootNode === null) {
            $rootNode = $method->getCrawler();
        }
        $result = $rootNode->filter($this->selector);

        return $this->applyResultCallbackChain($result, $method);
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
     * Apply all result callbacks
     *
     * @param Crawler $node
     * @param ApistMethod $method
     *
     * @return array|string|Crawler
     * @throws InvalidArgumentException
     */
    protected function applyResultCallbackChain(Crawler $node, ApistMethod $method)
    {
        return $this->resultMethodChain->call($node, $method->getBlueprintParser());
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

    /**
     * @param $e
     * @param ResultCallback[] $traceStack
     *
     * @return string
     */
    protected function createExceptionMessage(\Exception $e, $traceStack)
    {
        $message = "[ filter({$this->selector})";
        foreach ($traceStack as $callback) {
            $message .= '->' . $callback->getMethodName() . '(';
            try {
                $message .= implode(', ', $callback->getArguments());
            } catch (\Exception $_e) {
            }
            $message .= ')';
        }
        $message .= ' ] ' . $e->getMessage();

        return $message;
    }
}