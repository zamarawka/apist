<?php namespace SleepingOwl\Apist\Selectors;

use SleepingOwl\Apist\ApistConf;
use SleepingOwl\Apist\BlueprintParser;
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
     * @param Crawler $node
     * @param \SleepingOwl\Apist\BlueprintParser $parser
     *
     * @return array|string
     * @throws \InvalidArgumentException
     */
    public function apply(Crawler $node, BlueprintParser $parser)
    {
        /*if ($node->count() > 1) {
            return $this->applyToArray($node, $parser);
        }*/
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

        if ($this->isResourceMethod()) {
            return $this->callResourceMethod($node);
        }
        if ($this->isNodeMethod($node)) {
            return $this->callNodeMethod($node);
        }
        if ($this->isGlobalFunction()) {
            return $this->callGlobalFunction($node);
        }
        throw new \InvalidArgumentException("Method '{$this->methodName}' was not found");
    }

    /**
     * @param $array
     * @param \SleepingOwl\Apist\BlueprintParser $parser
     *
     * @return array
     */
    /*protected function applyToArray($array, BlueprintParser $parser)
    {
        $result = [];
        foreach ($array as $node) {
            $result[] = $this->apply($node, $parser);
        }

        return $result;
    }*/

    /**
     * @return bool
     */
    protected function isResourceMethod()
    {
        return method_exists(ApistConf::class, $this->methodName);
    }

    /**
     * @param $node
     *
     * @return mixed
     */
    protected function callResourceMethod($node)
    {
        $arguments = $this->arguments;
        array_unshift($arguments, $node);

        return call_user_func_array([
            ApistConf::class,
            $this->methodName
        ], $arguments);
    }

    /**
     * @param $node
     *
     * @return bool
     */
    protected function isNodeMethod($node)
    {
        return method_exists($node, $this->methodName);
    }

    /**
     * @param $node
     *
     * @return mixed
     */
    protected function callNodeMethod($node)
    {
        return call_user_func_array([
            $node,
            $this->methodName
        ], $this->arguments);
    }

    /**
     * @return bool
     */
    protected function isGlobalFunction()
    {
        return function_exists($this->methodName);
    }

    /**
     * @param $node
     *
     * @return mixed
     */
    protected function callGlobalFunction($node)
    {
        if (is_object($node)) {
            $node = $node->text();
        }
        $arguments = $this->arguments;
        array_unshift($arguments, $node);

        return call_user_func_array($this->methodName, $arguments);
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