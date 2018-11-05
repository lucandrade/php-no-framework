<?php declare(strict_types=1);

namespace App\Tests\Acceptance;

class RequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_hit_a_path()
    {
        $client = $this->webApp->makeClient();
        $response = $client->get('/');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $this->assertThat('testing', $this->equalTo($responseData['payload']));
    }
}
