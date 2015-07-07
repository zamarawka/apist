<?php
namespace SleepingOwl\Apist\Methods;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use SleepingOwl\Apist\ApistConf;
use SleepingOwl\Apist\Blueprint;
use SleepingOwl\Apist\DomCrawler\Crawler;
use SleepingOwl\Apist\Selectors\ParsingChain;

class ApistMethod
{
    /**
     * @var ApistConf
     */
    protected $guzzle;
    /**
     * @var Uri
     */
    protected $url;
    /**
     * @var ParsingChain[]|ParsingChain
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
     * @var \SleepingOwl\Apist\Blueprint
     */
    protected $blueprintParser;

    /**
     * @param ClientInterface $guzzle
     * @param Uri $url
     * @param $schemaBlueprint
     */
    public function __construct(ClientInterface $guzzle, Uri $url, $schemaBlueprint)
    {
        $this->guzzle = $guzzle;
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
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
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
        $blueprintParser = new Blueprint($this->crawler);

        return $blueprintParser->parse($this->schemaBlueprint, $this->getCrawler());
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

        $request = new Request($this->getMethod(), $this->url);
        $response = $this->guzzle->send($request, $arguments);
        $this->setResponse($response);
        $this->setContent((string) $response->getBody());
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

    /**
     * @return Blueprint
     */
    public function getBlueprintParser()
    {
        return $this->blueprintParser;
    }

}