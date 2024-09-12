<?php

namespace IDCT\Logger\Model;

use Monolog\LogRecord;

class AsyncLogMessage
{
    public function __construct(private LogRecord $logRecord)
    {

    }

    public function getLogRecord()
    {
        return $this->logRecord;
    }
}