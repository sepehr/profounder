<?php

namespace Profounder\Augment;

use Profounder\ResponseCrawler;
use Symfony\Component\DomCrawler\Crawler;

class ResponseParser extends ResponseCrawler
{
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
     * @inheritdoc
     *
     * @return ArticlePage
     */
    protected function parseBody($body)
    {
        return $this->makeArticlePage();
    }

    /**
     * Sets defaults property.
     *
     * @param  array $defaults
     *
     * @return $this
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
    private function makeArticlePage()
    {
        return new ArticlePage(
            $this->extractToc(),
            $this->extractFlatToc(),
            $this->extractLength(),
            $this->extractAbstract()
        );
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
        $abstract = $this->normalizeWhitespace(
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
    private function extractFlatToc()
    {
        if ($toc = $this->getTocElement()) {
            return $this->normalizeWhitespace($toc->parents()->text());
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
     * @param  Crawler $item
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
     * @param  Crawler $item
     *
     * @return array
     */
    private function buildTocItemArray(Crawler $item)
    {
        $built = array_replace([
            'title' => $this->extractTocItemTitle($item),
            'price' => $this->preparePrice($this->extractTocItemPrice($item)),
        ], $this->defaults);

        if ($children = $item->filter('ul > li') and $children->count()) {
            $built['children'] = $this->buildTocArray($children);
        }

        return $built;
    }

    /**
     * Prepares TOC item title.
     *
     * @param  Crawler $item
     *
     * @return string
     */
    private function extractTocItemTitle(Crawler $item)
    {
        return $this->normalizeWhitespace(
            str_replace($this->extractTocItemPrice($item), '', $item->filter('span.rtIn')->text())
        );
    }

    /**
     * Prepares TOC item price.
     *
     * @param  Crawler $item
     *
     * @return string|null
     */
    private function extractTocItemPrice(Crawler $item)
    {
        if ($price = $item->filter('span.rtIn > b') and $price->count()) {
            return $this->normalizeWhitespace($price->text());
        }

        return null;
    }

    /**
     * Converts a price string to equivalent integer.
     *
     * @param  string|null $price
     *
     * @return int|null
     */
    private function preparePrice($price)
    {
        return empty($price)
            ? $price
            : intval(preg_replace('/([^0-9\\.])/i', '', $price) * 100);
    }

    /**
     * Normalizes whitespace characters in a string.
     *
     * @param  string $string
     *
     * @return string
     */
    private function normalizeWhitespace($string)
    {
        return trim(preg_replace('!\s+!', ' ', str_replace("\xC2\xA0", ' ', $string)));
    }
}
