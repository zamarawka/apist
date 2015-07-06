<?php
namespace SleepingOwl\Apist\Methods;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use SleepingOwl\Apist\Apist;
use SleepingOwl\Apist\DomCrawler\Crawler;
use SleepingOwl\Apist\Selectors\ApistSelector;

class ApistMethod
{
    /**
     * @var Apist
     */
    protected $resource;
    /**
     * @var Uri
     */
    protected $url;
    /**
     * @var ApistSelector[]|ApistSelector
     */
    protected $schemaBlueprint;
    /**
     * @var string
     */
    protected $method = 'GET';
    /**
     * @var string
     */
    protected $content;
    /**
     * @var Crawler
     */
    protected $crawler;
    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    protected $response;

    /**
     * @param $resource
     * @param Uri $url
     * @param $schemaBlueprint
     */
    public function __construct($resource, Uri $url, $schemaBlueprint)
    {
        $this->resource = $resource;
        $this->url = $url;
        $this->schemaBlueprint = $schemaBlueprint;
        $this->crawler = new Crawler();
    }

    /**
     * Perform method action
     *
     * @param array $arguments
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(array $arguments = [])
    {
        try {
            $this->makeRequest($arguments);
        } catch (ConnectException $e) {
            $url = $e->getRequest()->getUri();

            return $this->errorResponse($e->getCode(), $e->getMessage(), $url);
        } catch (RequestException $e) {
            $url = $e->getRequest()->getUri();
            $status = $e->getCode();
            $response = $e->getResponse();
            $reason = $e->getMessage();
            if ($response !== null) {
                $reason = $response->getReasonPhrase();
            }

            return $this->errorResponse($status, $reason, $url);
        }

        return $this->parseBlueprint($this->schemaBlueprint);
    }

    /**
     * Make http request
     *
     * @param array $arguments
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function makeRequest(array $arguments = [])
    {
        $defaults = $this->getDefaultOptions();
        $arguments = array_merge($defaults, $arguments);
        $client = $this->resource->getGuzzle();

        $request = new Request($this->getMethod(), $this->url);
        $response = $client->send($request, $arguments);
        $this->setResponse($response);
        $this->setContent((string) $response->getBody());
    }

    /**
     * @param $blueprint
     * @param null $node
     *
     * @return array|string
     */
    public function parseBlueprint($blueprint, $node = null)
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
     */
    protected function parseBlueprintValue($value, $node)
    {
        if ($value instanceof ApistSelector) {
            return $value->getValue($this, $node);
        }

        return $value;
    }

    /**
     * Response with error
     *
     * @param $status
     * @param $reason
     * @param $url
     *
     * @return array
     */
    protected function errorResponse($status, $reason, $url)
    {
        return [
            'url'   => $url,
            'error' => [
                'status' => $status,
                'reason' => $reason,
            ]
        ];
    }

    /**
     * @return Crawler
     */
    public function getCrawler()
    {
        return $this->crawler;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        $this->crawler->addContent($content);

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'cookies' => true
        ];
    }

    /**
     * @return Apist
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return \GuzzleHttp\Psr7\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \GuzzleHttp\Psr7\Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

}