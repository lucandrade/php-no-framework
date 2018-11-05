<?php declare(strict_types=1);

require_once '../vendor/autoload.php';

if (!isset($_SERVER['APP_ENV']) && !isset($_ENV['APP_ENV'])) {
    (new \Dotenv\Dotenv(__DIR__ . '/../'))->load();
}

$container = new \App\Containers\AppContainer();
$kernel = new \App\AppKernel($container->get(\App\AppRouter::class), $container);

$kernel->handle();
