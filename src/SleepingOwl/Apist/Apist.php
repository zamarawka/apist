<?php
/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 06.07.15
 * Time: 15:53
 */

namespace SleepingOwl\Apist;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Uri;
use SleepingOwl\Apist\Methods\ApistMethod;

class Apist
{
    use SuppressExceptionTrait;
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
     * @param \GuzzleHttp\ClientInterface $httpClient
     * @param string $baseUrl
     */
    public function __construct(ClientInterface $httpClient, $baseUrl)
    {
        $this->guzzle = $httpClient;
        $this->baseUrl = Uri::fromParts(parse_url($baseUrl));
    }

    /**
     * @param $content
     * @param $blueprint
     *
     * @return array|string
     */
    /*public function parse($content, $blueprint)
    {
        $this->currentMethod = new ApistMethod($this, null, $blueprint);
        $this->currentMethod->setContent($content);
        $result = $this->currentMethod->parseBlueprint($blueprint);
        $this->currentMethod = null;

        return $result;
    }*/

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
        $this->currentMethod = new ApistMethod($this->getGuzzle(), $url, $blueprint);
        $this->lastMethod = $this->currentMethod;
        $this->currentMethod->setMethod($httpMethod);
        $result = $this->currentMethod->get($options);
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
     * @return ApistMethod
     */
    public function getCurrentMethod()
    {
        return $this->currentMethod;
    }

    /**
     * @return ApistMethod
     */
    public function getLastMethod()
    {
        return $this->lastMethod;
    }

    /**
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = Uri::fromParts(parse_url($baseUrl));
    }
}