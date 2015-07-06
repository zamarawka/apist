<?php
/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 06.07.15
 * Time: 16:02
 */

namespace SleepingOwl\Apist;


use SleepingOwl\Apist\Methods\ApistMethod;
use SleepingOwl\Apist\Selectors\ApistSelector;

class BlueprintParser
{
    /**
     * @var ApistMethod
     */
    protected $method;

    /**
     * BlueprintParser constructor.
     *
     * @param \SleepingOwl\Apist\Methods\ApistMethod $method
     */
    public function __construct(ApistMethod $method)
    {
        $this->method = $method;
    }

    /**
     * @param $blueprint
     * @param null $node
     *
     * @return array|string
     * @throws \InvalidArgumentException
     */
    public function parse($blueprint, $node = null)
    {
        if ($blueprint === null) {
            return $this->content;
        }
        if (!is_array($blueprint)) {
            $blueprint = $this->parseBlueprintValue($blueprint, $node);
        }
        else {
            array_walk_recursive($blueprint, function (&$value) use ($node) {
                $value = $this->parseBlueprintValue($value, $node);
            });
        }

        return $blueprint;
    }

    /**
     * @param $value
     * @param $node
     *
     * @return array|string
     * @throws \InvalidArgumentException
     */
    protected function parseBlueprintValue($value, $node)
    {
        if ($value instanceof ApistSelector) {
            return $value->getValue($this->method, $node);
        }

        return $value;
    }
}