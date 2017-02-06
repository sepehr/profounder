<?php

namespace Profounder;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

abstract class ResponseCrawler extends ResponseParser
{
    /**
     * Crawler instance.
     *
     * @var Crawler
     */
    protected $crawler;

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
     * @inheritdoc
     */
    public function setResponse(ResponseInterface $response)
    {
        parent::setResponse($response);

        // Crawler does not support loading multiple documents anymore,
        // See: https://github.com/symfony/symfony/pull/16057/files
        $this->crawler->clear();

        $this->crawler->addContent($this->responseBody());

        return $this;
    }
}
