<?php
/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 07.07.15
 * Time: 10:51
 */

namespace SleepingOwl\Apist;

use SleepingOwl\Apist\Selectors\ParsingChain;

class BlueprintConfigParser
{
    /**
     * @var array
     */
    protected $methods = [];
    /**
     * @var array
     */
    protected $structures = [];

    /**
     * @param array $config
     */
    public function parse(array $config = [])
    {
        foreach ($config as $method => $methodConfig) {
            if ($method[0] === '_') {
                # structure
                $this->structures[$method] = $methodConfig;
            }
            else {
                # method
                if (!isset($methodConfig['blueprint'])) {
                    $methodConfig['blueprint'] = null;
                }
                $methodConfig['blueprint'] =
                    $this->parseBlueprint($methodConfig['blueprint']);
                $this->methods[$method] = $methodConfig;
            }
        }
    }

    /**
     * @param $blueprint
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function parseBlueprint($blueprint)
    {
        $callback = function (&$value) {
            if (is_string($value)) {
                $value = str_replace(':current', '*', $value);
            }
            if ($value[0] === ':') {
                # structure
                $structure = $this->getStructure($value);
                $value = $this->parseBlueprint($structure);

                return;
            }
            if (strpos($value, '|') === false) {
                return;
            }

            $parts = preg_split('/\s?\|\s?/', $value);
            $selector = array_shift($parts);
            $value = ApistConf::select($selector);
            foreach ($parts as $part) {
                $this->addCallbackToFilter($value, $part);
            }
        };
        if (!is_array($blueprint)) {
            $callback($blueprint);
        }
        else {
            array_walk_recursive($blueprint, $callback);
        }

        return $blueprint;
    }

    /**
     * @param ParsingChain $filter
     * @param $callback
     *
     * @throws \InvalidArgumentException
     */
    protected function addCallbackToFilter(ParsingChain $filter, $callback)
    {
        $method = strtok($callback, '(),');
        $arguments = [];
        while (($argument = strtok('(),')) !== false) {
            $argument = trim($argument);
            if (preg_match('/^[\'"].*[\'"]$/', $argument)) {
                $argument = substr($argument, 1, -1);
            }
            if ($argument[0] === ':') {
                # structure
                $structure = $this->getStructure($argument);
                $argument = $this->parseBlueprint($structure);
            }
            $arguments[] = $argument;
        }
        $filter->addCallback($method, $arguments);
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function getStructure($name)
    {
        $structure = '_' . substr($name, 1);
        if (!isset($this->structures[$structure])) {
            throw new \InvalidArgumentException("Structure '$structure' not found.'");
        }

        return $this->structures[$structure];
    }

    /**
     * @param $name
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getMethod($name)
    {
        if (!isset($this->methods[$name])) {
            throw new \InvalidArgumentException("Method '$name' not found.'");
        }
        $methodConfig = $this->methods[$name];

        return $methodConfig;
    }
}