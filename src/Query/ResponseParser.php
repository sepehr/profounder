<?php

namespace Profounder\Query;

use Profounder\JsonResponseParser;
use Profounder\Exception\InvalidSession;
use Profounder\Exception\InvalidResponse;

class ResponseParser extends JsonResponseParser
{
    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function parseBody($parsedJson)
    {
        return $parsedJson['Results'] ?: [];
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
}
