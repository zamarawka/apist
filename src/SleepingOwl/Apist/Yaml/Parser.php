<?php namespace SleepingOwl\Apist\Yaml;

use SleepingOwl\Apist\Apist;
use SleepingOwl\Apist\ApistConf;
use SleepingOwl\Apist\BlueprintConfigParser;
use SleepingOwl\Apist\Selectors\ParsingChain;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Parser
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
     * @var string
     */
    protected $file;

    /**
     * @var \SleepingOwl\Apist\BlueprintConfigParser
     */
    protected $blueprintParser;

    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->blueprintParser = new BlueprintConfigParser();
    }

    /**
     * @param Apist $resource
     *
     * @throws ParseException
     * @throws \InvalidArgumentException
     */
    public function load(Apist $resource)
    {
        $data = Yaml::parse(file_get_contents($this->file));
        if (isset($data['baseUrl'])) {
            $resource->setBaseUrl($data['baseUrl']);
            unset($data['baseUrl']);
        }
        $this->blueprintParser->parse($data);
    }

    /**
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public function insertMethodArguments($method, $arguments)
    {
        array_walk_recursive($method, function (&$value) use ($arguments) {
            if (!is_string($value)) {
                return;
            }
            $value = preg_replace_callback('/\$(?<num>[0-9]+)/',
                function ($finded) use ($arguments) {
                    $argumentPosition = (int) $finded['num'] - 1;

                    return isset($arguments[$argumentPosition]) ?
                        $arguments[$argumentPosition] : null;
                }, $value);
        });

        return $method;
    }



    /**
     * @param $name
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getMethod($name)
    {
        return $this->blueprintParser->getMethod($name);
    }
}