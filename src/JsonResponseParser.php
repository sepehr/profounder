<?php

namespace Profounder;

use Profounder\Exception\InvalidResponse;

abstract class JsonResponseParser extends ResponseParser
{
    /**
     * Parsed JSON array.
     *
     * @var array
     */
    protected $parsedJson;

    /**
     * @inheritdoc
     */
    protected function validate()
    {
        parent::validate();

        if (is_null($this->parseJson())) {
            throw InvalidResponse::invalidJson();
        }

        $this->validateJson($this->jsonResponse());
    }

    /**
     * @inheritdoc
     *
     * Let the parseBody() method of derived classes receive the parsed JSON array instead of the raw response body.
     *
     * @return array
     */
    protected function prepareBodyForParse()
    {
        return $this->jsonResponse();
    }

    /**
     * Validates the response JSON array.
     *
     * Derived classes should override this method to implement their JSON validation checks.
     *
     * @param  array $parsedJson
     *
     * @return void
     */
    protected function validateJson(array $parsedJson)
    {
        //
    }

    /**
     * Parses a JSON response into an array.
     *
     * @return array|null
     */
    protected function parseJson()
    {
        return $this->parsedJson = json_decode($this->responseBody(), true);
    }

    /**
     * Returns parsed JSON array.
     *
     * @return array|null
     */
    protected function jsonResponse()
    {
        return $this->parsedJson;
    }
}
