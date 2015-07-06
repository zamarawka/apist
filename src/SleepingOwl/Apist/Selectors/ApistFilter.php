<?php
namespace SleepingOwl\Apist\Selectors;

use SleepingOwl\Apist\BlueprintParser;
use SleepingOwl\Apist\DomCrawler\Crawler;

/**
 * Class ApistFilter
 *
 * @method ApistFilter else($blueprint)
 */
class ApistFilter
{
    /**
     * @var Crawler
     */
    protected $node;
    /**
     * @var BlueprintParser
     */
    protected $parser;

    /**
     * @param mixed $node
     * @param \SleepingOwl\Apist\BlueprintParser $parser
     */
    public function __construct($node, BlueprintParser $parser)
    {
        $this->node = $node;
        $this->parser = $parser;
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function text()
    {
        $this->guardCrawler();

        return $this->node->text();
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function html()
    {
        $this->guardCrawler();

        return $this->node->html();
    }

    /**
     * @param $selector
     *
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function filter($selector)
    {
        $this->guardCrawler();

        return $this->node->filter($selector);
    }

    /**
     * @param $selector
     *
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function filterNodes($selector)
    {
        $this->guardCrawler();
        $rootNode = $this->method->getCrawler();
        $crawler = new Crawler;
        $rootNode->filter($selector)
                 ->each(function (Crawler $filteredNode) use ($crawler) {
                     $filteredNode = $filteredNode->getNode(0);
                     foreach ($this->node as $node) {
                         if ($filteredNode === $node) {
                             $crawler->add($node);
                             break;
                         }
                     }
                 });

        return $crawler;
    }

    /**
     * @param $selector
     *
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function find($selector)
    {
        $this->guardCrawler();

        return $this->node->filter($selector);
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function children()
    {
        $this->guardCrawler();

        return $this->node->children();
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function prev()
    {
        $this->guardCrawler();

        return $this->prevAll()->first();
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function prevAll()
    {
        $this->guardCrawler();

        return $this->node->previousAll();
    }

    /**
     * @param $selector
     *
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function prevUntil($selector)
    {
        return $this->nodeUntil($selector, 'prev');
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function next()
    {
        $this->guardCrawler();

        return $this->nextAll()->first();
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function nextAll()
    {
        $this->guardCrawler();

        return $this->node->nextAll();
    }

    /**
     * @param $selector
     *
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function nextUntil($selector)
    {
        return $this->nodeUntil($selector, 'next');
    }

    /**
     * @param $selector
     * @param $direction
     *
     * @return Crawler
     * @throws \InvalidArgumentException
     */
    public function nodeUntil($selector, $direction)
    {
        $this->guardCrawler();
        $crawler = new Crawler;
        $filter = new static($this->node, $this->parser);
        while (1) {
            $node = $filter->$direction();
            if (null === $node) {
                break;
            }
            $filter->node = $node;
            if ($filter->is($selector)) {
                break;
            }
            $crawler->add($node->getNode(0));
        }

        return $crawler;
    }

    /**
     * @param $selector
     *
     * @return \SleepingOwl\Apist\Selectors\ApistFilter
     * @throws \InvalidArgumentException
     */
    public function is($selector)
    {
        $this->guardCrawler();

        return count($this->filterNodes($selector)) > 0;
    }

    /**
     * @param $selector
     *
     * @return \SleepingOwl\Apist\Selectors\ApistFilter
     * @throws \InvalidArgumentException
     */
    public function closest($selector)
    {
        $this->guardCrawler();
        $this->node = $this->node->parents();

        return $this->filterNodes($selector)->last();
    }

    /**
     * @param $attribute
     *
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function attr($attribute)
    {
        $this->guardCrawler();

        return $this->node->attr($attribute);
    }

    /**
     * @param $attribute
     *
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function hasAttr($attribute)
    {
        $this->guardCrawler();

        return $this->node->attr($attribute) !== null;
    }

    /**
     * @param $position
     *
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function eq($position)
    {
        $this->guardCrawler();

        return $this->node->eq($position);
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function first()
    {
        $this->guardCrawler();

        return $this->node->first();
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function last()
    {
        $this->guardCrawler();

        return $this->node->last();
    }

    /**
     * @return ApistFilter
     */
    public function element()
    {
        return $this->node;
    }

    /**
     * @param $callback
     *
     * @return ApistFilter
     */
    public function call($callback)
    {
        return $callback($this->node);
    }

    /**
     * @param string $mask
     *
     * @return \SleepingOwl\Apist\Selectors\ApistFilter
     * @throws \InvalidArgumentException
     */
    public function trim($mask = " \t\n\r\0\x0B")
    {
        $this->guardText();

        return trim($this->node, $mask);
    }

    /**
     * @param string $mask
     *
     * @return \SleepingOwl\Apist\Selectors\ApistFilter
     * @throws \InvalidArgumentException
     */
    public function ltrim($mask = " \t\n\r\0\x0B")
    {
        $this->guardText();

        return ltrim($this->node, $mask);
    }

    /**
     * @param string $mask
     *
     * @return \SleepingOwl\Apist\Selectors\ApistFilter
     * @throws \InvalidArgumentException
     */
    public function rtrim($mask = " \t\n\r\0\x0B")
    {
        $this->guardText();

        return rtrim($this->node, $mask);
    }

    /**
     * @param $search
     * @param $replace
     * @param null $count
     *
     * @return \SleepingOwl\Apist\Selectors\ApistFilter
     * @throws \InvalidArgumentException
     */
    public function str_replace($search, $replace, $count = null)
    {
        $this->guardText();

        return str_replace($search, $replace, $this->node, $count);
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function intval()
    {
        $this->guardText();

        return (int) $this->node;
    }

    /**
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function floatval()
    {
        $this->guardText();

        return (float) $this->node;
    }

    /**
     * @return ApistFilter
     */
    public function exists()
    {
        return count($this->node) > 0;
    }

    /**
     * @param $callback
     *
     * @return ApistFilter
     */
    public function check($callback)
    {
        return $this->call($callback);
    }

    /**
     * @param $blueprint
     *
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function then($blueprint)
    {
        if ($this->node === true) {
            return $this->parser->parse($blueprint);
        }

        return $this->node;
    }

    /**
     * @param $blueprint
     *
     * @return ApistFilter
     * @throws \InvalidArgumentException
     */
    public function each($blueprint = null)
    {
        $callback = $blueprint;
        if ($callback === null) {
            $callback = function ($node) {
                return $node;
            };
        }
        if (!is_callable($callback)) {
            $callback = function ($node) use ($blueprint) {
                return $this->parser->parse($blueprint, $node);
            };
        }

        return $this->node->each($callback);
    }

    /**
     * Guard string method to be called with Crawler object
     * @throws \InvalidArgumentException
     */
    protected function guardText()
    {
        if (is_object($this->node)) {
            $this->node = $this->node->text();
        }
    }

    /**
     * Guard method to be called with Crawler object
     * @throws \InvalidArgumentException
     */
    protected function guardCrawler()
    {
        if (!$this->node instanceof Crawler) {
            throw new \InvalidArgumentException('Current node isnt instance of Crawler.');
        }
    }

}
