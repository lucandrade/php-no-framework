<?php declare(strict_types=1);

namespace App;

use App\Responses\ApiResponse;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Psr\Log\LoggerInterface;
use Teapot\StatusCode;

class AppRouter
{
    /** @var Router */
    private $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function build(ContainerInterface $container): Router
    {
        $appStrategy = $this->buildStrategy($container);
        $this->router->setStrategy($appStrategy);

        $this->router->get('/', function (ServerRequestInterface $request) {
            return ApiResponse::success('hello world');
        });

        $this->router->get('/error', function (ServerRequestInterface $request) {
            trigger_error('Log me!');
            trigger_error('Log me too!');
            return ApiResponse::success('All errors are logged');
        });

        $this->router->get('/error-caught', function (ServerRequestInterface $request) {
            try {
                throw new \Exception('Message');
            } catch (\Exception $e) {
                return ApiResponse::success('got you');
            }
        });

        $this->router->get('/fatal-error', function (ServerRequestInterface $request) {
            unexistent_function();
            return ApiResponse::success('got you');
        });

        return $this->router;
    }

    private function buildStrategy(ContainerInterface $container)
    {
        $strategy = new class extends ApplicationStrategy {
            public function getNotFoundDecorator(NotFoundException $exception) : MiddlewareInterface
            {
                return new class($exception) implements MiddlewareInterface
                {
                    protected $exception;

                    public function __construct(\Exception $exception)
                    {
                        $this->exception = $exception;
                    }

                    public function process(
                        ServerRequestInterface $request,
                        RequestHandlerInterface $requestHandler
                    ) : ResponseInterface {
                        return ApiResponse::error('Error', StatusCode::NOT_FOUND;
                    }
                };
            }
        };

        $strategy->setContainer($container);
        return $strategy;
    }
}
