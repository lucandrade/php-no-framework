<?php declare(strict_types=1);

namespace App\Log;

use App\Containers\ServiceProviderInterface;
use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /** @var string */
    private const LOG_ID = 'Application';

    public static function register(Container $container): void
    {
        $container->set(LoggerInterface::class, function () {
            $logger = new Logger(self::LOG_ID);
            $logger->pushHandler(new StreamHandler(__DIR__ . '/../../storage/logs/dev.log', Logger::DEBUG));

            return $logger;
        });
    }
}
