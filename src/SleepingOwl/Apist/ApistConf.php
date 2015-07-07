<?php
namespace SleepingOwl\Apist;

use SleepingOwl\Apist\Selectors\ParsingChain;
use SleepingOwl\Apist\Yaml\YamlApist;

class ApistConf
{
    /**
     * Get current node
     *
     * @return \SleepingOwl\Apist\Selectors\ParsingChain
     */
    public static function current()
    {
        return static::select('*');
    }

    /**
     * Create filter object
     *
     * @param $cssSelector
     *
     * @return \SleepingOwl\Apist\Selectors\ParsingChain
     */
    public static function select($cssSelector)
    {
        return new ParsingChain($cssSelector);
    }

    /**
     * Initialize api from yaml configuration file
     *
     * @param $file
     *
     * @return YamlApist
     */
    public static function fromYaml($file)
    {
        return new YamlApist($file, []);
    }
}
