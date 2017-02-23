<?php

namespace Profounder;

use Symfony\Component\DomCrawler\Crawler;

class StateParser extends CrawlableParser
{
    /**
     * State fields CSS selector.
     *
     * @var string
     */
    protected $stateSelector = 'input[type="hidden"]';

    /**
     * @inheritdoc
     *
     * @return State
     */
    protected function parseBody($body)
    {
        return $this->makeStateInstance();
    }

    /**
     * Creates an State instance out of the response.
     *
     * @return State
     */
    private function makeStateInstance()
    {
        $data = [];
        $this->crawler->filter($this->stateSelector)->each(function (Crawler $node) use (&$data) {
            $data[$node->attr('name')] = $node->attr('value');
        });

        return State::create([
            'data'   => $data,
            'cookie' => $this->response->getHeader('Set-Cookie'),
        ]);
    }
}
