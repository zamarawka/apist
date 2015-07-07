<?php
/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 07.07.15
 * Time: 11:14
 */

namespace SleepingOwl\Apist\Selectors;


use SleepingOwl\Apist\ApistConf;
use Symfony\Component\DomCrawler\Crawler;

class CallbackCallResolver
{
    protected $methodName;
    protected $arguments;
    protected $node;

    /**
     * CallbackCallResolver constructor.
     *
     * @param $arguments
     * @param $node
     */
    public function __construct($arguments, $node)
    {
        $this->arguments = $arguments;
        $this->node = $node;
    }

    /**
     * Finds and calls a function.
     *
     * @param $name
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function resolve($name)
    {
        $this->methodName = $name;
        if ($this->isResourceMethod()) {
            return $this->callResourceMethod();
        }
        if ($this->isNodeMethod()) {
            return $this->callNodeMethod();
        }
        if ($this->isGlobalFunction()) {
            return $this->callGlobalFunction();
        }

        throw new \InvalidArgumentException("Method '{$this->methodName}' was not found");
    }

    /**
     * @return bool
     */
    protected function isResourceMethod()
    {
        return method_exists(ApistConf::class, $this->methodName);
    }

    /**
     * @return mixed
     */
    protected function callResourceMethod()
    {
        $arguments = $this->arguments;
        array_unshift($arguments, $this->node);

        return call_user_func_array([
            ApistConf::class,
            $this->methodName
        ], $arguments);
    }

    /**
     * @return bool
     */
    protected function isNodeMethod()
    {
        return method_exists($this->node, $this->methodName);
    }

    /**
     * @return mixed
     */
    protected function callNodeMethod()
    {
        return call_user_func_array([
            $this->node,
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
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function callGlobalFunction()
    {
        $node = $this->node;
        if ($node instanceof Crawler) {
            $node = $node->text();
        }
        $arguments = $this->arguments;
        array_unshift($arguments, $node);

        return call_user_func_array($this->methodName, $arguments);
    }
}