<?php
/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 06.07.15
 * Time: 16:31
 */

namespace SleepingOwl\Apist\Selectors;

use SleepingOwl\Apist\BlueprintParser;
use SleepingOwl\Apist\SuppressExceptionTrait;
use Symfony\Component\DomCrawler\Crawler;

class ResultCallbackChain extends \ArrayObject
{
    use SuppressExceptionTrait;
    /**
     * ResultCallbackChain constructor.
     */
    public function __construct()
    {
        parent::__construct([], $flags = 0, $iterator_class = "ArrayIterator");
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $node
     *
     * @param \SleepingOwl\Apist\BlueprintParser $parser
     *
     * @return array|null|string|\Symfony\Component\DomCrawler\Crawler
     * @throws \InvalidArgumentException
     */
    public function call(Crawler $node, BlueprintParser $parser)
    {
        if ($this->count() === 0) {
            $this->addCallback('text');
        }
        /** @var ResultCallback $resultCallback */
        $traceStack = [];
        foreach ($this->getIterator() as $resultCallback) {
            try {
                $traceStack[] = $resultCallback;
                $node = $resultCallback->apply($node, $parser);
            } catch (\InvalidArgumentException $e) {
                if ($this->isSuppressExceptions()) {
                    return null;
                }
                $message = $this->createExceptionMessage($e, $traceStack);
                throw new \InvalidArgumentException($message, 0, $e);
            }
        }

        return $node;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return $this
     */
    public function addCallback($name, $arguments = [])
    {
        $resultCallback = new ResultCallback($name, $arguments);
        $this->append($resultCallback);

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
        $message = '[filter_chain';
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