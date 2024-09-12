<?php declare(strict_types=1);

namespace IDCT\Logger\Handler;

use Closure;
use IDCT\Logger\Model\AsyncLogMessage;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossed\ActivationStrategyInterface;
use Monolog\Level;
use Monolog\Logger;
use Monolog\ResettableInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\Handler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\ProcessableHandlerTrait;
use Psr\Log\LogLevel;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Buffers all records until a certain level is reached
 *
 * The advantage of this approach is that you don't get any clutter in your log files.
 * Only requests which actually trigger an error (or whatever your actionLevel is) will be
 * in the logs, but they will contain all records, not only those above the level threshold.
 *
 * You can then have a passthruLevel as well which means that at the end of the request,
 * even if it did not get activated, it will still send through log records of e.g. at least a
 * warning level.
 *
 * You can find the various activation strategies in the
 * Monolog\Handler\FingersCrossed\ namespace.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class AsyncMessageHandler extends AbstractHandler
{
    protected MessageBusInterface $messageBus;

    #[Required]
    public function setMessageBus(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __construct(int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    /**
    * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $this->messageBus->dispatch(new AsyncLogMessage($record));
    }

        /**
     * @inheritDoc
     */
    public function handle(LogRecord $record): bool
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        $this->write($record);

        return false === $this->bubble;
    }

}