<?php
namespace SleepingOwl\Apist;

use SleepingOwl\Apist\Selectors\ApistSelector;
use SleepingOwl\Apist\Yaml\YamlApist;

class ApistConf
{
    /**
     * Get current node
     *
     * @return \SleepingOwl\Apist\Selectors\ApistSelector
     */
    public static function current()
    {
        return static::filter('*');
    }

    /**
     * Create filter object
     *
     * @param $cssSelector
     *
     * @return \SleepingOwl\Apist\Selectors\ApistSelector
     */
    public static function filter($cssSelector)
    {
        return new ApistSelector($cssSelector);
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
