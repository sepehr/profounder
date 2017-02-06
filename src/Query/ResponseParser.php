<?php

namespace Profounder\Query;

use Illuminate\Support\Collection;
use Profounder\JsonResponseParser;
use Profounder\Exception\InvalidSession;
use Profounder\Exception\InvalidResponse;

class ResponseParser extends JsonResponseParser
{
    /**
     * Parses response into a collection of CollectedArticle objects.
     *
     * @inheritdoc
     *
     * @return Collection
     */
    protected function parseBody($parsedJson)
    {
        return $this->makeCollection($parsedJson);
    }

    /**
     * @inheritdoc
     *
     * @throws InvalidResponse
     * @throws InvalidSession
     */
    protected function validateJson(array $parsedJson)
    {
        if ($parsedJson['UserIsLoggedOut']) {
            throw InvalidSession::expired();
        }

        if (! empty($parsedJson['ErrorMessage'])) {
            throw InvalidResponse::remoteError($parsedJson['ErrorMessage']);
        }
    }

    /**
     * Creates a collection of CollectedArticle objects out of the JSON results.
     *
     * @param  array $parsedJson
     *
     * @return Collection
     */
    private function makeCollection(array $parsedJson)
    {
        return Collection::make($parsedJson['Results'])->map(function ($result) {
            return CollectedArticle::create($result);
        });
    }
}
