idct/symfony-async-monolog-handler
==================================

Simple toolchain for Symfony 6+ framework and Monolog which allows sending logs using the symfony/messenger asynchronously.

# Installation
```
composer require idct/symfony-async-monolog-handler
```

# Usage

As this is not a bundle, but a library which acts as a toolchain you need to execute three steps before it actually works:

1. Register the service in your `services.yaml`:
```yaml
    monolog.handler.async-stream:
        class: IDCT\Logger\Handler\AsyncMessageHandler
        public: false
```

2. Choose which real handler should be actually executed. For example if you have:

```yaml
    file_log:
        type: stream
        # log to var/log/(environment).log
        path: "%kernel.logs_dir%/async-test.log"
        # log *all* messages (debug is lowest level)
        level: debug
```

in your `monolog.yaml` then add to `services.yaml`:

```yaml
    IDCT\Logger\Messenger\AsyncLogMessageHandler:
        arguments:
            - '@monolog.handler.file_log'
```

3. Add you asynchronous channel, for example name it `async`, then to `monolog.yaml` add:

```yaml
monolog:
    channels:
        - async
```

4. In `monolog.yaml` register your async proxy logger:

```yaml
    async-stream:
        type: 'service'
        id: 'monolog.handler.async-stream'
        channels: ['async']
        level: debug
```

where `id` must match the identifier of a service from step 1.

5. Whenever you want to use your async logger inject it using standard symfony + monolog naming convention which include channel's name:

```yaml
    public function __construct(protected LoggerInterface $asyncLogger)
    {
        
    }
```

6. Register a transport for your async messages in `messenger.yaml`:

```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'

        routing:
            # Route your messages to the transports
            'IDCT\Logger\Model\AsyncLogMessage': async
```

7. Be sure to set `MESSENGER_TRANSPORT_DSN` env variable.

8. Activate symfony/messenger.

9. Note: Target handler will handle the message even if its channels' list does not match. This can be useful to filter out other messages, for example the target handler may be set to ignore messenger logs:
```yaml
    file_log:
        type: stream
        # log to var/log/(environment).log
        path: "%kernel.logs_dir%/async-test.log"
        # log *all* messages (debug is lowest level)
        channels: ['!messenger']
        level: debug
```

# Contribution

Any contribution towards better testing is more than welcome. In `tests/func` you can already find a preconfigured symfony which tests the solution when `app:test` (TestCommand.php) is executed.