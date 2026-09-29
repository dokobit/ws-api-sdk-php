<?php
namespace Dokobit\Tests\Http;

use Dokobit\Exception\InvalidApiKey;
use Dokobit\Exception\InvalidData;
use Dokobit\Exception\ServerError;
use Dokobit\Exception\Timeout;
use Dokobit\Exception\UnexpectedError;
use Dokobit\Exception\UnexpectedResponse;
use GuzzleHttp\Exception\ClientException;
use Dokobit\Http\GuzzleClientAdapter;
use GuzzleHttp\Psr7\Utils;

class GuzzleClientAdapterTest extends \PHPUnit\Framework\TestCase
{
    private $adapter;
    private $client;

    public function setUp(): void
    {
        $this->client = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapter = new GuzzleClientAdapter($this->client);
    }

    public function testPost()
    {
        $this->client
            ->method('request')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo('https://developers.dokobit.com'),
                []
            )
            ->willReturn(
                $response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
                    ->disableOriginalConstructor()
                    ->getMock()
            );

        $response
            ->expects($this->once())
            ->method('getBody')
        ;

        $this->client
            ->expects($this->once())
            ->method('request')
        ;

        $this->adapter->sendRequest('POST', 'https://developers.dokobit.com');
    }

    public function testDataValidationError400()
    {
        $this->expectExceptionCode(400);
        $this->expectException(InvalidData::class);
        $request = $this->getMockBuilder('Psr\Http\Message\RequestInterface')->getMock();
        $response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')->getMock();

        $response
            ->method('getStatusCode')
            ->willReturn(400)
        ;
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(Utils::streamFor('response body'))
        ;

        $this->client
            ->method('request')
            ->will(
                $this->throwException(
                    new ClientException(
                        'Error',
                        $request,
                        $response
                    )
                )
            )
        ;

        $this->adapter->sendRequest('POST', 'https://developers.dokobit.com');
    }

    public function testInvalidApiKeyError403()
    {
        $this->expectExceptionCode(403);
        $this->expectException(InvalidApiKey::class);
        $request = $this->getMockBuilder('Psr\Http\Message\RequestInterface')->getMock();
        $response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')->getMock();

        $response
            ->method('getStatusCode')
            ->willReturn(403)
        ;
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(Utils::streamFor('response body'))
        ;

        $this->client
            ->method('request')
            ->will(
                $this->throwException(
                    new ClientException(
                        'Error',
                        $request,
                        $response
                    )
                )
            )
        ;

        $this->adapter->sendRequest('POST', 'https://developers.dokobit.com');
    }

    public function testServerError500()
    {
        $this->expectException(ServerError::class);
        $this->expectExceptionCode(500);
        $request = $this->getMockBuilder('Psr\Http\Message\RequestInterface')->getMock();
        $response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')->getMock();

        $response
            ->method('getStatusCode')
            ->willReturn(500)
        ;
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(Utils::streamFor('response body'))
        ;

        $this->client
            ->method('request')
            ->will(
                $this->throwException(
                    new ClientException(
                        'Error',
                        $request,
                        $response
                    )
                )
            )
        ;

        $this->adapter->sendRequest('POST', 'https://developers.dokobit.com');
    }

    public function testTimeout504()
    {
        $this->expectExceptionCode(504);
        $this->expectException(Timeout::class);
        $request = $this->getMockBuilder('Psr\Http\Message\RequestInterface')->getMock();
        $response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')->getMock();

        $response
            ->method('getStatusCode')
            ->willReturn(504)
        ;
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(Utils::streamFor('response body'))
        ;

        $this->client
            ->method('request')
            ->will(
                $this->throwException(
                    new ClientException(
                        'Error',
                        $request,
                        $response
                    )
                )
            )
        ;

        $this->adapter->sendRequest('POST', 'https://developers.dokobit.com');
    }

    public function testUnexpectedResponseStatusCode()
    {
        $this->expectExceptionCode(101);
        $this->expectException(UnexpectedResponse::class);
        $request = $this->getMockBuilder('Psr\Http\Message\RequestInterface')->getMock();
        $response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')->getMock();

        $response
            ->method('getStatusCode')
            ->willReturn(101)
        ;
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(Utils::streamFor('response body'))
        ;

        $this->client
            ->method('request')
            ->will(
                $this->throwException(
                    new ClientException(
                        'Error',
                        $request,
                        $response
                    )
                )
            )
        ;

        $this->adapter->sendRequest('POST', 'https://developers.dokobit.com');
    }

    public function testUnexpectedError()
    {
        $this->expectException(UnexpectedError::class);
        $this->client
            ->method('request')
            ->will(
                $this->throwException(
                    new \Exception()
                )
            )
        ;

        $this->adapter->sendRequest('POST', 'https://developers.dokobit.com');
    }
}
