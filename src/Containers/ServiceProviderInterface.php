<?php declare(strict_types=1);

namespace App\Containers;

use DI\Container;

interface ServiceProviderInterface
{
    public static function register(Container $container): void;
}
