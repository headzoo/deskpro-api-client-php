<?php

/*
 * DeskPRO (r) has been developed by DeskPRO Ltd. https://www.deskpro.com/
 * a British company located in London, England.
 *
 * All source code and content Copyright (c) 2017, DeskPRO Ltd.
 *
 * The license agreement under which this software is released
 * can be found at https://www.deskpro.com/eula/
 *
 * By using this software, you acknowledge having read the license
 * and agree to be bound thereby.
 *
 * Please note that DeskPRO is not free software. We release the full
 * source code for our software because we trust our users to pay us for
 * the huge investment in time and energy that has gone into both creating
 * this software and supporting our customers. By providing the source code
 * we preserve our customers' ability to modify, audit and learn from our
 * work. We have been developing DeskPRO since 2001, please help us make it
 * another decade.
 *
 * Like the work you see? Think you could make it better? We are always
 * looking for great developers to join us: http://www.deskpro.com/jobs/
 *
 * ~ Thanks, Everyone at Team DeskPRO
 */

namespace Deskpro\API;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Makes requests to the Deskpro API.
 */
class DeskproClient implements DeskproClientInterface
{
    use LoggerAwareTrait;

    /**
     * Base API path
     */
    const API_PATH = '/api/v2';

    /**
     * The authentication header
     */
    const AUTH_HEADER = 'Authorization';

    /**
     * Key to use for token authentication
     */
    const AUTH_TOKEN_KEY = 'token';

    /**
     * Key to use for key authentication
     */
    const AUTH_KEY_KEY = 'key';

    /**
     * Logs are prefixed with this string
     */
    const LOG_PREFIX = 'DeskproClient';
    
    /**
     * @var string
     */
    protected $helpdeskUrl;
    
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var URLInterpolatorInterface
     */
    protected $urlInterpolator;

    /**
     * @var string
     */
    protected $authToken;

    /**
     * @var string
     */
    protected $authKey;

    /**
     * @var array
     */
    protected $defaultHeaders = [];

    /**
     * @var RequestInterface
     */
    protected $lastHTTPRequest;

    /**
     * @var ResponseInterface
     */
    protected $lastHTTPResponse;

    /**
     * @var RequestException
     */
    protected $lastHTTPRequestException;

    /**
     * Constructor
     * 
     * @param string          $helpdeskUrl The base URL to the DeskPRO instance
     * @param ClientInterface $httpClient  HTTP client used to make requests
     * @param LoggerInterface $logger      Used to log requests
     */
    public function __construct($helpdeskUrl, ClientInterface $httpClient = null, LoggerInterface $logger = null)
    {
        $this->setHelpdeskUrl($helpdeskUrl);
        $this->setHTTPClient($httpClient ?: new Client());
        $this->setURLInterpolator(new URLInterpolator());
        $this->setLogger($logger ?: new NullLogger());
    }

    /**
     * {@inheritdoc}
     */
    public function getHelpdeskUrl()
    {
        return $this->helpdeskUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setHelpdeskUrl($helpdeskUrl)
    {
        $this->helpdeskUrl = rtrim($helpdeskUrl, '/');
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHTTPClient()
    {
        return $this->httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function setHTTPClient(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getURLInterpolator()
    {
        return $this->urlInterpolator;
    }

    /**
     * {@inheritdoc}
     */
    public function setURLInterpolator(URLInterpolatorInterface $urlInterpolator)
    {
        $this->urlInterpolator = $urlInterpolator;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthToken($personId, $token)
    {
        $this->authToken = sprintf("%d:%s", $personId, $token);
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthKey($personId, $key)
    {
        $this->authKey = sprintf("%d:%s", $personId, $key);
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultHeaders()
    {
        return $this->defaultHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultHeaders(array $defaultHeaders)
    {
        $this->defaultHeaders = $defaultHeaders;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastHTTPRequest()
    {
        return $this->lastHTTPRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastHTTPResponse()
    {
        return $this->lastHTTPResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastHTTPRequestException()
    {
        return $this->lastHTTPRequestException;
    }

    /**
     * {@inheritdoc}
     */
    public function get($endpoint, array $params = [])
    {
        return $this->request('GET', $endpoint, null, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getAsync($endpoint, array $params = [])
    {
        return $this->requestAsync('GET', $endpoint, null, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function post($endpoint, $body = null, array $params = [])
    {
        return $this->request('POST', $endpoint, $body, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function postAsync($endpoint, $body = null, array $params = [])
    {
        return $this->requestAsync('POST', $endpoint, $body, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function put($endpoint, $body = null, array $params = [])
    {
        return $this->request('PUT', $endpoint, $body, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function putAsync($endpoint, $body = null, array $params = [])
    {
        return $this->requestAsync('PUT', $endpoint, $body, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($endpoint, array $params = [])
    {
        return $this->request('DELETE', $endpoint, null, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAsync($endpoint, array $params = [])
    {
        return $this->requestAsync('DELETE', $endpoint, null, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function request($method, $endpoint, $body = null, array $params = [], array $headers = [])
    {
        $endpoint = $this->urlInterpolator->interpolate($endpoint, $params);
        
        try {
            $this->lastHTTPRequestException = null;
            $this->lastHTTPRequest  = $this->makeRequest($method, $endpoint, $body, $headers);
            $this->lastHTTPResponse = $this->httpClient->send($this->lastHTTPRequest);
            
            return $this->makeResponse($this->lastHTTPResponse->getBody());
        } catch (RequestException $e) {
            $this->lastHTTPRequestException = $e;
            throw $this->makeException($e->getResponse()->getBody());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function requestAsync($method, $endpoint, $body = null, array $params = [], array $headers = [])
    {
        $endpoint = $this->urlInterpolator->interpolate($endpoint, $params);
        $this->lastHTTPRequestException = null;
        $this->lastHTTPRequest = $this->makeRequest($method, $endpoint, $body, $headers);
        
        return $this->httpClient->sendAsync($this->lastHTTPRequest)
            ->then(function(ResponseInterface $resp) {
                $this->lastHTTPResponse = $resp;
                return $this->makeResponse($resp->getBody());
            }, function (RequestException $e) {
                $this->lastHTTPRequestException = $e;
                throw $this->makeException($e->getResponse()->getBody());
            });
    }

    /**
     * @param array $headers
     * 
     * @return array
     */
    protected function makeHeaders(array $headers = [])
    {
        $headers = array_merge($this->defaultHeaders, $headers);
        if (!isset($headers[self::AUTH_HEADER])) {
            if ($this->authToken) {
                $headers[self::AUTH_HEADER] = sprintf('%s %s', self::AUTH_TOKEN_KEY, $this->authToken);
            } else if ($this->authKey) {
                $headers[self::AUTH_HEADER] = sprintf('%s %s', self::AUTH_KEY_KEY, $this->authKey);
            }
        }

        return $headers;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param mixed $body
     * @param array $headers
     * 
     * @return Request
     */
    protected function makeRequest($method, $endpoint, $body = null, array $headers = [])
    {
        $url = sprintf('%s%s/%s', $this->helpdeskUrl, self::API_PATH, trim($endpoint, '/'));
        $headers = $this->makeHeaders($headers);
        if (is_array($body) && isset($body['multipart'])) {
            $body = new MultipartStream($body['multipart']);
        } else if ($body !== null && !is_scalar($body)) {
            $body = json_encode($body);
        }
        $this->logger->debug(sprintf('%s: %s %s', self::LOG_PREFIX, $method, $url), [
            'headers' => $headers,
            'body'    => $body
        ]);
        
        return new Request($method, $url, $headers, $body);
    }

    /**
     * @param string $body
     * 
     * @return APIResponseInterface|mixed
     */
    protected function makeResponse($body)
    {
        $decoded = json_decode($body, true);
        if ($decoded === null) {
            return $body;
        }
        if (is_array($decoded) && (!isset($decoded['data']) || !isset($decoded['meta']) || !isset($decoded['linked']))) {
            return $decoded;
        }

        return new APIResponse($decoded['data'], $decoded['meta'], $decoded['linked']);
    }

    /**
     * @param string $body
     * 
     * @return Exception\APIException
     */
    protected function makeException($body)
    {
        $body = json_decode($body, true);
        if ($body === null || !isset($body['status']) || !isset($body['message'])) {
            return new Exception\MalformedResponseException('Could not JSON decode API response.', 0);
        }

        switch($body['status']) {
            case 401:
                return new Exception\AuthenticationException($body['message'], 401);
                break;
            case 403:
                return new Exception\AccessDeniedException($body['message'], 403);
                break;
            case 404:
                return new Exception\NotFoundException($body['message'], 404);
                break;
        }

        return new Exception\APIException($body['message'], (int)$body['status']);
    }
}
