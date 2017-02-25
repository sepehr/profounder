<?php

namespace Profounder\Auth\Http\Parser;

use Profounder\Auth\Session\Session;
use Profounder\Exception\InvalidResponse;
use Profounder\Foundation\Http\Parser\Parser as BaseParser;

class Parser extends BaseParser implements ParserContract
{
    /**
     * @inheritdoc
     */
    protected function validate()
    {
        if ($this->responseContains('unsuccessful login')) {
            throw InvalidResponse::invalidCredentials();
        }

        if ($this->responseContains('captcha authentication failed')) {
            throw InvalidResponse::captchaRequired();
        }

        parent::validate();
    }

    /**
     * @inheritdoc
     *
     * @return Session
     */
    protected function parseBody($body)
    {
        return Session::create([
            'cookie' => $this->response->getHeader('Set-Cookie')
        ]);
    }
}
