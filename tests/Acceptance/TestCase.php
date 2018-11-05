<?php declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Acceptance\Support\WebApp;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var WebApp */
    protected $webApp;

    /**
     * @before
     */
    public function setUpWebServer()
    {
        $this->webApp = new WebApp();
        $this->webApp->startWebServer();
    }
}
