<?php
/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 06.07.15
 * Time: 16:02
 */

namespace SleepingOwl\Apist;

use SleepingOwl\Apist\Selectors\ParsingChain;

class Blueprint
{
    /**
     * @param $blueprint
     * @param null $node
     *
     * @return array|string
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function parse($blueprint, $node = null)
    {
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
     * @throws \RuntimeException
     */
    protected function parseBlueprintValue($value, $node)
    {
        if ($value instanceof ParsingChain) {
            return $value->getValue($node, $this);
        }

        return $value;
    }
}