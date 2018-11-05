<?php declare(strict_types=1);

namespace App\Containers;

use DI\Container;
use DI\ContainerBuilder;
use App\AppRouter;
use Psr\Container\ContainerInterface;

final class AppContainer implements ContainerInterface
{
    /** @var Container */
    private $container;

    public function __construct()
    {
        $this->container = $this->build();
        $this->boot();
    }

    private function build(): Container
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(false);
        $builder->useAnnotations(false);

        return $builder->build();
    }

    private function boot(): void
    {
        \App\Log\ServiceProvider::register($this->container);
        $this->container->set(AppRouter::class, function (Container $container) {
            $appRouter = new AppRouter();
            return $appRouter->build($container);
        });
    }

    public function get($id)
    {
        return $this->container->get($id);
    }

    public function has($id)
    {
        return $this->container->has($id);
    }
}
