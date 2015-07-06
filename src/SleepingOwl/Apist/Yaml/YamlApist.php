<?php namespace SleepingOwl\Apist\Yaml;

use SleepingOwl\Apist\Apist;

class YamlApist extends Apist
{
	/**
	 * @var Parser
	 */
	protected $parser;

    /**
     * @param null $file
     * @param array $options
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
	public function __construct($file = null, $options = [])
	{
		if ($file !== null)
		{
			$this->loadFromYml($file);
		}
		parent::__construct($options);
	}

	/**
	 * Load method data from yaml file
	 * @param $file
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \InvalidArgumentException
	 */
	protected function loadFromYml($file)
	{
		$this->parser = new Parser($file);
		$this->parser->load($this);
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return array
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function __call($name, $arguments)
	{
		if ($this->parser === null)
		{
			throw new \InvalidArgumentException("Method '$name' not found.'");
		}
		$method = $this->parser->getMethod($name);
		$method = $this->parser->insertMethodArguments($method, $arguments);
		$httpMethod = isset($method['method']) ? strtoupper($method['method']) : 'GET';
		$options = isset($method['options']) ? $method['options'] : [];

		return $this->request($httpMethod, $method['url'], $method['blueprint'], $options);
	}

} 