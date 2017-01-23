<?php

namespace App\Exceptions;

use Exception;

class Handler
{
    public function report(Exception $exception)
    {
        print $exception->getMessage();
    }
}
