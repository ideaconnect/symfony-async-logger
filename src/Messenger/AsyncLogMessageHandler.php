<?php

declare(strict_types=1);

namespace IDCT\Logger\Messenger;

use IDCT\Logger\Model\AsyncLogMessage;
use Monolog\Handler\HandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AsyncLogMessageHandler
{
    public function __construct(protected HandlerInterface $actualHandler)
    {
    }

    public function __invoke(AsyncLogMessage $message): void
    {
        $record = $message->getLogRecord();
        $this->actualHandler->handle($record);
    }
}