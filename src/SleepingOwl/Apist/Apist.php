<?php
namespace SleepingOwl\Apist;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Uri;
use SleepingOwl\Apist\Methods\ApistMethod;
use SleepingOwl\Apist\Selectors\ApistSelector;
use SleepingOwl\Apist\Yaml\YamlApist;

abstract class Apist
{
    /**
     * @var Uri
     */
    protected $baseUrl;
    /**
     * @var ClientInterface
     */
    protected $guzzle;
    /**
     * @var ApistMethod
     */
    protected $currentMethod;
    /**
     * @var ApistMethod
     */
    protected $lastMethod;
    /**
     * @var bool
     */
    protected $suppressExceptions = true;

    /**
     * @param \GuzzleHttp\ClientInterface $httpClient
     * @param string $baseUrl
     */
    public function __construct(ClientInterface $httpClient, $baseUrl)
    {
        $this->guzzle = $httpClient;
        $this->baseUrl = Uri::fromParts(parse_url($baseUrl));
    }

    /**
     * @return ClientInterface
     */
    public function getGuzzle()
    {
        return $this->guzzle;
    }

    /**
     * @param \GuzzleHttp\ClientInterface $guzzle
     */
    public function setGuzzle(ClientInterface $guzzle)
    {
        $this->guzzle = $guzzle;
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
     * Get current node
     *
     * @return \SleepingOwl\Apist\Selectors\ApistSelector
     */
    public static function current()
    {
        return static::filter('*');
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

    /**
     * @return ApistMethod
     */
    public function getCurrentMethod()
    {
        return $this->currentMethod;
    }

    /**
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return ApistMethod
     */
    public function getLastMethod()
    {
        return $this->lastMethod;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = Uri::fromParts(parse_url($baseUrl));
    }

    /**
     * @return boolean
     */
    public function isSuppressExceptions()
    {
        return $this->suppressExceptions;
    }

    /**
     * @param boolean $suppressExceptions
     */
    public function setSuppressExceptions($suppressExceptions)
    {
        $this->suppressExceptions = $suppressExceptions;
    }

    /**
     * @param $httpMethod
     * @param $url
     * @param $blueprint
     * @param array $options
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request($httpMethod, $url, $blueprint, $options = [])
    {
        $url = Uri::resolve($this->getBaseUrl(), $url);
        $this->currentMethod = new ApistMethod($this, $url, $blueprint);
        $this->lastMethod = $this->currentMethod;
        $this->currentMethod->setMethod($httpMethod);
        $result = $this->currentMethod->get($options);
        $this->currentMethod = null;

        return $result;
    }

    /**
     * @param $content
     * @param $blueprint
     *
     * @return array|string
     */
    protected function parse($content, $blueprint)
    {
        $this->currentMethod = new ApistMethod($this, null, $blueprint);
        $this->currentMethod->setContent($content);
        $result = $this->currentMethod->parseBlueprint($blueprint);
        $this->currentMethod = null;

        return $result;
    }

    /**
     * @param $url
     * @param $blueprint
     * @param array $options
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function get($url, $blueprint = null, $options = [])
    {
        return $this->request('GET', $url, $blueprint, $options);
    }

    /**
     * @param $url
     * @param $blueprint
     * @param array $options
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function head($url, $blueprint = null, $options = [])
    {
        return $this->request('HEAD', $url, $blueprint, $options);
    }

    /**
     * @param $url
     * @param $blueprint
     * @param array $options
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function post($url, $blueprint = null, $options = [])
    {
        return $this->request('POST', $url, $blueprint, $options);
    }

    /**
     * @param $url
     * @param $blueprint
     * @param array $options
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function put($url, $blueprint = null, $options = [])
    {
        return $this->request('PUT', $url, $blueprint, $options);
    }

    /**
     * @param $url
     * @param $blueprint
     * @param array $options
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function patch($url, $blueprint = null, $options = [])
    {
        return $this->request('PATCH', $url, $blueprint, $options);
    }

    /**
     * @param $url
     * @param $blueprint
     * @param array $options
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function delete($url, $blueprint = null, $options = [])
    {
        return $this->request('DELETE', $url, $blueprint, $options);
    }

}
