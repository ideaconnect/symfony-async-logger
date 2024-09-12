<?php
// src/MessageHandler/SmsNotificationHandler.php
namespace IDCT\Logger\Messenger;

use App\Message\SmsNotification;
use IDCT\Logger\Model\AsyncLogMessage;
use Monolog\Handler\HandlerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AsyncLogMessageHandler
{
    public function __construct(protected HandlerInterface $actualHandler)
    {
    }

    public function __invoke(AsyncLogMessage $message)
    {
        /** @var AsyncLogMessage */
        $record = $message->getLogRecord();

        $this->actualHandler->handle($record);
    }
}