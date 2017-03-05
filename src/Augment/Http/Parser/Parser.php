<?php

namespace Profounder\Augment\Http\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Profounder\Foundation\Support\Utils;
use Profounder\Foundation\Http\Parser\CrawlableParser;

class Parser extends CrawlableParser implements ParserContract
{
    /**
     * Utils instance.
     *
     * @var Utils
     */
    private $utils;

    /**
     * TOC item defaults array.
     *
     * @var array
     */
    private $defaults = [];

    /**
     * TOC, length and abstract elements CSS selectors.
     *
     * @var array
     */
    private $selectors = [
        'abstract' => '#BodyArea_MainContentArea_multiItemRepeater_productAbstract_0',
        'length'   => '#BodyArea_MainContentArea_multiItemRepeater_TOCKwicInfotd_0 > div.basicInfoSection',
        'toc'      => '#BodyArea_MainContentArea_multiItemRepeater_TOC_0_TOCTree_0 > ul > li > ul.rtUL > li',
    ];

    /**
     * Parser constructor.
     *
     * @param Crawler $crawler
     * @param Utils   $utils
     */
    public function __construct(Crawler $crawler, Utils $utils)
    {
        $this->utils = $utils;

        parent::__construct($crawler);
    }

    /**
     * @inheritdoc
     *
     * @return ArticlePage
     */
    protected function parseResponse($body)
    {
        return $this->makeArticlePageInstance();
    }

    /**
     * @inheritdoc
     */
    public function withDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * Creates an ArticlePage instance.
     *
     * @return ArticlePage
     */
    private function makeArticlePageInstance()
    {
        return ArticlePage::create([
            'toc'      => $this->extractToc(),
            'length'   => $this->extractLength(),
            'toctext'  => $this->extractTocText(),
            'abstract' => $this->extractAbstract(),
        ]);
    }

    /**
     * Extracts article length from the response body.
     *
     * @return int|null
     */
    private function extractLength()
    {
        $content = $this->crawler->filter($this->selectors['length'])->text();

        if (preg_match('/(\d+)\sPages/mi', $content, $matches) && isset($matches[1])) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Extracts article abstract from the response body.
     *
     * @return string|null
     */
    private function extractAbstract()
    {
        $abstract = $this->utils->normalizeWhitespace(
            $this->crawler->filter($this->selectors['abstract'])->html()
        );

        return $abstract ?: null;
    }

    /**
     * Extracts TOC from the response content and converts it into an array.
     *
     * @return array|null
     */
    private function extractToc()
    {
        if ($toc = $this->getTocElement()) {
            return $this->buildTocArray($toc);
        }

        return null;
    }

    /**
     * Extracts TOC into a flat string.
     *
     * @return string|null
     */
    private function extractTocText()
    {
        if ($toc = $this->getTocElement()) {
            return $this->utils->normalizeWhitespace($toc->parents()->text());
        }

        return null;
    }

    /**
     * Returns TOC element crawler, if available.
     *
     * @return Crawler|null
     */
    private function getTocElement()
    {
        $subCrawler = $this->crawler->filter($this->selectors['toc']);

        return $subCrawler->count() === 1 ? null : $subCrawler;
    }

    /**
     * Builds an array out of TOC html structure.
     *
     * @param Crawler $item
     *
     * @return array
     */
    private function buildTocArray(Crawler $item)
    {
        if (! $item->count()) {
            return [];
        }

        return $item->each(function (Crawler $item) {
            return $this->buildTocItemArray($item);
        });
    }

    /**
     * Builds a TOC item array.
     *
     * @param Crawler $item
     *
     * @return array
     */
    private function buildTocItemArray(Crawler $item)
    {
        $built = array_replace([
            'title' => $this->extractTocItemTitle($item),
            'price' => $this->utils->preparePrice($this->extractTocItemPrice($item)),
        ], $this->defaults);

        if ($children = $item->filter('ul > li') and $children->count()) {
            $built['children'] = $this->buildTocArray($children);
        }

        return $built;
    }

    /**
     * Prepares TOC item title.
     *
     * @param Crawler $item
     *
     * @return string
     */
    private function extractTocItemTitle(Crawler $item)
    {
        return $this->utils->normalizeWhitespace(
            str_replace($this->extractTocItemPrice($item), '', $item->filter('span.rtIn')->text())
        );
    }

    /**
     * Prepares TOC item price.
     *
     * @param Crawler $item
     *
     * @return string|null
     */
    private function extractTocItemPrice(Crawler $item)
    {
        if ($price = $item->filter('span.rtIn > b') and $price->count()) {
            return $this->utils->normalizeWhitespace($price->text());
        }

        return null;
    }
}
