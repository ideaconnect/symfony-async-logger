<?php

declare(strict_types=1);

namespace IDCT\Logger\Model;

use Monolog\LogRecord;

class AsyncLogMessage
{
    public function __construct(private LogRecord $logRecord)
    {

    }

    public function getLogRecord(): LogRecord
    {
        return $this->logRecord;
    }
}