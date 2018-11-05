<?php declare(strict_types=1);

namespace App;

use App\Responses\ApiResponse;
use League\Route\Router;
use Psr\Container\ContainerInterface;
use Teapot\StatusCode;
use Throwable;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

class AppKernel
{
    /** @var Router */
    private $router;

    /** @var ContainerInterface */
    private $container;

    public function __construct(Router $router, ContainerInterface $container)
    {
        $this->router = $router;
        $this->container = $container;

        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    private function logError(
        int $errorCode,
        string $errorMessage,
        string $file = '',
        int $line = 0,
        array $context = []
    ): void {
        if ($this->container->has(LoggerInterface::class)) {
            $this->container->get(LoggerInterface::class)->error($errorMessage, [
                'code' => $errorCode,
                'file' => $file,
                'line' => $line,
                'context' => $context,
            ]);
        }
    }

    public function handleException(Throwable $throwable): void
    {
        $this->logError(
            $throwable->getCode(),
            $throwable->getMessage(),
            $throwable->getFile(),
            $throwable->getLine()
        );

        (new SapiEmitter())->emit(ApiResponse::error(
            'Fail',
            StatusCode::INTERNAL_SERVER_ERROR)
        );
    }

    public function handleError(
        int $errorCode,
        string $errorMessage,
        string $file = '',
        int $line = 0,
        array $context = []
    ): void {
        $this->logError($errorCode, $errorMessage, $file, $line, $context);
    }

    public function handle(): void
    {
        $response = $this->router->dispatch(ServerRequestFactory::fromGlobals());

        (new SapiEmitter())->emit($response);
    }
}
