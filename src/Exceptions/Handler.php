<?php

namespace Profounder\Exceptions;

use Exception;

class Handler
{
    public function report(Exception $exception)
    {
        print $exception->getMessage();
    }
}
//$this->log->error($e->getMessage());
//$this->files->put($logfile, $responseBody);
//
//throw $e;
