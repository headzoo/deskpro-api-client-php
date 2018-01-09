<?php
use PHPUnit\Framework\TestCase;
use DeskPRO\API\DeskPROClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/**
 * @coversDefaultClass \DeskPRO\API\DeskPROClient
 */
class DeskPROClientTest extends TestCase
{
    /**
     * @covers ::get
     */
    public function testGet()
    {
        $body = [
            'data' => [
                [
                    'id' => 101,
                    'person' => 352,
                    'language' => 1,
                    'slug' => 'exercitationem-illo-quod-et-provident',
                    'title' => 'Exercitationem illo quod et provident',
                    'content' => 'Duchess: flamingoes and mustard both bite. And the Eaglet bent down its head down.',
                    'view_count' => 33,
                    'total_rating' => 15,
                    'num_comments' => 1,
                    'num_ratings' => 14,
                    'status' => 'published',
                    'date_created' => '2017-11-16T13:40:04+0000',
                    'date_updated' => '2017-11-27T12:43:34+0000',
                    'date_published' => '2017-11-27T12:43:34+0000'
                ]
            ],
            'meta' => [
                'count' => 1
            ],
            'linked' => []
        ];

        $client = $this->getMockClient([
            new Response(200, [], json_encode($body))
        ]);
        $resp = $client->get('/articles');
        $data = $resp->getData();
        $meta = $resp->getMeta();
        
        $this->assertEquals($data[0], $body['data'][0]);
        $this->assertEquals($meta['count'], $body['meta']['count']);
    }

    /**
     * @param Response[] $responses
     * @return DeskPROClient
     */
    private function getMockClient(array $responses)
    {
        $mock       = new MockHandler($responses);
        $handler    = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handler]);
        $client     = new DeskPROClient('http://deskpro-dev.com', $httpClient);
        
        return $client;
    }
}