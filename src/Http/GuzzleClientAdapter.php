<?php
namespace Dokobit\Http;

use GuzzleHttp;
use GuzzleHttp\Exception\RequestException;
use Dokobit\Exception;

/**
 * Adapter for GuzzleHttp client
 */
class GuzzleClientAdapter implements ClientInterface
{
    /** @var GuzzleHttp\ClientInterface */
    private $client;

    /**
     * @param type GuzzleHttp\ClientInterface $client
     */
    public function __construct(GuzzleHttp\ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Send HTTP request
     * @param string $method POST|GET
     * @param string $url http URL
     * @param array $options query options. Query values goes under 'form_params' key.
     * Example:
     * $options = [
     *     'query' => [
     *         'access_token' => 'foobar',
     *     ],
     *     'form_params' => [
     *         'param1' => 'value1',
     *         'param2' => 'value2',
     *     ]
     * ]
     * @return array
     */
    public function sendRequest($method, $url, array $options = [])
    {
        $result = [];

        try {
            $response = $this->client->request($method, $url, $options);
            $result = json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() == 400) {
                throw new Exception\InvalidData(
                    'Data validation failed',
                    400,
                    $e,
                    json_decode($e->getResponse()->getBody(), true)
                );
            } elseif ($e->getCode() == 403) {
                throw new Exception\InvalidApiKey(
                    'Access forbidden. Invalid API key.',
                    403,
                    $e,
                    $e->getResponse()->getBody()
                );
            } elseif ($e->getCode() == 500) {
                throw new Exception\ServerError(
                    'Error occurred on server side while handling request',
                    500,
                    $e,
                    $e->getResponse()->getBody()
                );
            } elseif ($e->getCode() == 504) {
                throw new Exception\Timeout(
                    'Request timeout',
                    504,
                    $e,
                    $e->getResponse()->getBody()
                );
            } else {
                throw new Exception\UnexpectedResponse(
                    'Unexpected error occurred',
                    $e->getCode(),
                    $e,
                    $e->getResponse()->getBody()
                );
            }
        } catch (\Exception $e) {
            throw new Exception\UnexpectedError('Unexpected error occurred', 0, $e);
        }

        return $result;
    }
}
