<?php namespace SleepingOwl\Apist\Selectors;

use SleepingOwl\Apist\Blueprint;
use Symfony\Component\DomCrawler\Crawler;

class ResultCallback
{
    /**
     * @var string
     */
    protected $methodName;
    /**
     * @var array
     */
    protected $arguments;

    /**
     * @param $methodName
     * @param $arguments
     */
    public function __construct($methodName, $arguments)
    {
        $this->methodName = $methodName;
        $this->arguments = $arguments;
    }

    /**
     * Apply result callback to the $node, provided by $method
     *
     * @param Crawler|array|bool $node
     * @param \SleepingOwl\Apist\Blueprint $parser
     *
     * @return array|string
     * @throws \InvalidArgumentException
     */
    public function apply($node, Blueprint $parser)
    {
        if (is_array($node)) {
            return $this->applyToArray($node, $parser);
        } else {
            return $this->innerApply($node, $parser);
        }

    }

    /**
     * @param $node
     * @param \SleepingOwl\Apist\Blueprint $parser
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function innerApply($node, Blueprint $parser)
    {
        if ($this->methodName === 'else') {
            if (is_bool($node)) {
                $node = !$node;
            }
            $this->methodName = 'then';
        }

        $filter = new ApistFilter($node, $parser);
        if (method_exists($filter, $this->methodName)) {
            return call_user_func_array([
                $filter,
                $this->methodName
            ], $this->arguments);
        }

        $resolver = new CallbackCallResolver($this->arguments, $node);

        return $resolver->resolve($this->methodName);
    }

    /**
     * @param $array
     * @param \SleepingOwl\Apist\Blueprint $parser
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function applyToArray($array, Blueprint $parser)
    {
        $result = [];
        foreach ($array as $node) {
            $result[] = $this->innerApply($node, $parser);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
} 