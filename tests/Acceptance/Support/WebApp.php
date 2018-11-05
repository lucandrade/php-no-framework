<?php declare(strict_types=1);

namespace App\Tests\Acceptance\Support;

use GuzzleHttp\Client;

class WebApp
{
    /** @var string */
    private const HOST = '127.0.0.1:3000';

    /** @var string */
    private const ENTRY_POINT = 'public/';

    /** @var null */
    private static $localWebServerId = null;

    public function startWebServer()
    {
        $this->launchWebServer();
        $this->waitUntilWebServerAcceptsRequests();
        $this->stopWebserverOnShutdown();
    }

    private function launchWebServer()
    {
        if (isset(self::$localWebServerId)) {
            return;
        }
        $command = sprintf(
            'php -S %s -t %s >/dev/null 2>&1 & echo $!',
            self::HOST,
            __DIR__.'/../../../'.self::ENTRY_POINT
        );
        exec($command, $output, $returnVar);
        self::$localWebServerId = (int) $output[0];
    }

    private function waitUntilWebServerAcceptsRequests()
    {
        exec('bash '.__DIR__.'/wait-for-it.sh -t 5 '.self::HOST, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception(
                "Unable to find webserver. Please check your have the correct paths configured."
            );
        }
    }

    private function stopWebServerOnShutdown()
    {
        register_shutdown_function(function () {
            exec('kill '.self::$localWebServerId);
        });
    }

    public function makeClient(): Client
    {
        return new Client([
            'base_uri' => 'http://'.self::HOST,
            'http_errors' => false,
        ]);
    }
}
