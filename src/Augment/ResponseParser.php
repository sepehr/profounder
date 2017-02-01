<?php

namespace Profounder\Augment;

use Psr\Http\Message\ResponseInterface;
use Profounder\Exception\InvalidSession;
use Profounder\Exception\InvalidResponse;
use Profounder\Exception\InvalidArgument;
use Symfony\Component\DomCrawler\Crawler;

class ResponseParser
{
    /**
     * Response object.
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * Crawler instance.
     *
     * @var Crawler
     */
    private $crawler;

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
     * TOC item defaults array.
     *
     * @var array
     */
    private $tocItemDefaults = [];

    /**
     * ResponseParser constructor.
     *
     * @param  Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Parses the HTML response into an ArticlePage entity.
     *
     * @param  ResponseInterface|null $response
     *
     * @return ArticlePage
     *
     * @throws InvalidArgument
     */
    public function parse(ResponseInterface $response = null)
    {
        $response && $this->setResponse($response);

        if (! $this->response) {
            throw new InvalidArgument('No response is set for the parser.');
        }

        $this->validate();

        return $this->makeArticlePage();
    }

    /**
     * Response setter.
     *
     * @param  ResponseInterface $response
     *
     * @return ResponseParser
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        $this->crawler->addHtmlContent((string) $response->getBody());

        return $this;
    }

    /**
     * Sets tocItemDefaults property.
     *
     * @param  array $defaults
     *
     * @return $this
     */
    public function withTocItemDefaults(array $defaults)
    {
        $this->tocItemDefaults = $defaults;

        return $this;
    }

    /**
     * Validates the HTML response content.
     *
     * @return bool
     *
     * @throws InvalidSession
     * @throws InvalidResponse
     */
    private function validate()
    {
        $content = (string) $this->response->getBody();

        if (strpos($content, 'web server encountered a critical error') || strpos($content, 'Runtime Error')) {
            throw InvalidResponse::critical();
        }

        if (strpos($content, 'Sign In')) {
            throw InvalidSession::expired();
        }

        if (strpos($content, 'One or more of your selected products were not found')) {
            throw InvalidResponse::notFound();
        }

        return true;
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
        $crawler = $this->crawler->filter($this->selectors['toc']);

        return $crawler->count() === 1 ? null : $crawler;
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
        ], $this->tocItemDefaults);

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
