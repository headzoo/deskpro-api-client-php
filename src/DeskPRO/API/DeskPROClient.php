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

namespace DeskPRO\API;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Makes requests to the DeskPRO API.
 */
class DeskPROClient
{
    use LoggerAwareTrait;

    /**
     * Base API path
     */
    const API_PATH = '/api/v2';
    
    /**
     * @var string
     */
    protected $helpdeskUrl;
    
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $authToken;

    /**
     * @var string
     */
    protected $authKey;

    /**
     * Constructor
     * 
     * @param string $helpdeskUrl
     * @param ClientInterface $httpClient
     * @param LoggerInterface $logger
     */
    public function __construct($helpdeskUrl, ClientInterface $httpClient = null, LoggerInterface $logger = null)
    {
        $this->setHelpdeskUrl($helpdeskUrl);
        $this->setHTTPClient($httpClient ?: new Client());
        $this->setLogger($logger ?: new NullLogger());
    }

    /**
     * @return string
     */
    public function getHelpdeskUrl()
    {
        return $this->helpdeskUrl;
    }
    
    /**
     * @param string $helpdeskUrl
     * @return $this
     */
    public function setHelpdeskUrl($helpdeskUrl)
    {
        $this->helpdeskUrl = rtrim($helpdeskUrl, '/');
        
        return $this;
    }

    /**
     * @return ClientInterface
     */
    public function getHTTPClient()
    {
        return $this->httpClient;
    }

    /**
     * @param ClientInterface $httpClient
     * @return $this
     */
    public function setHTTPClient(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        
        return $this;
    }

    /**
     * @param int $personId
     * @param string $token
     * @return $this
     */
    public function setAuthToken($personId, $token)
    {
        $this->authToken = sprintf("%d:%s", $personId, $token);
        
        return $this;
    }

    /**
     * @param int $personId
     * @param string $key
     * @return $this
     */
    public function setAuthKey($personId, $key)
    {
        $this->authKey = sprintf("%d:%s", $personId, $key);
        
        return $this;
    }

    /**
     * @param string $endpoint
     * @return Response
     * @throws Exception\APIException
     */
    public function get($endpoint)
    {
        return $this->request('GET', $endpoint);
    }

    /**
     * @param string $endpoint
     * @return PromiseInterface
     */
    public function getAsync($endpoint)
    {
        return $this->requestAsync('GET', $endpoint);
    }

    /**
     * @param string $endpoint
     * @param mixed $body
     * @return Response
     * @throws Exception\APIException
     */
    public function post($endpoint, $body = null)
    {
        return $this->request('POST', $endpoint, $body);
    }

    /**
     * @param string $endpoint
     * @param mixed $body
     * @return PromiseInterface
     */
    public function postAsync($endpoint, $body = null)
    {
        return $this->requestAsync('POST', $endpoint, $body);
    }

    /**
     * @param string $endpoint
     * @param mixed $body
     * @return Response
     * @throws Exception\APIException
     */
    public function put($endpoint, $body = null)
    {
        return $this->request('PUT', $endpoint, $body);
    }

    /**
     * @param string $endpoint
     * @param mixed $body
     * @return PromiseInterface
     */
    public function putAsync($endpoint, $body = null)
    {
        return $this->requestAsync('PUT', $endpoint, $body);
    }

    /**
     * @param string $endpoint
     * @return Response
     * @throws Exception\APIException
     */
    public function delete($endpoint)
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * @param string $endpoint
     * @return PromiseInterface
     */
    public function deleteAsync($endpoint)
    {
        return $this->requestAsync('DELETE', $endpoint);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param mixed $body
     * @param array $headers
     * @return Response
     * @throws Exception\APIException
     */
    public function request($method, $endpoint, $body = null, array $headers = [])
    {
        try {
            $req = $this->makeRequest($method, $endpoint, $body, $headers);
            $res = $this->httpClient->send($req);
            
            return $this->makeResponse($res->getBody());
        } catch (ClientException $e) {
            throw $this->makeException($e->getResponse()->getBody());
        }
    }

    /**
     * @param $method
     * @param $endpoint
     * @param null $body
     * @param array $headers
     * @return PromiseInterface
     */
    public function requestAsync($method, $endpoint, $body = null, array $headers = [])
    {
        $req = $this->makeRequest($method, $endpoint, $body, $headers);
        
        return $this->httpClient->sendAsync($req)
            ->then(function(ResponseInterface $resp) {
                return $this->makeResponse($resp->getBody());
            }, function (RequestException $e) {
                throw $this->makeException($e->getResponse()->getBody());
            });
    }

    /**
     * @param array $headers
     * @return array
     */
    protected function makeHeaders(array $headers = [])
    {
        if ($this->authToken) {
            $headers['Authorization'] = sprintf('token %s', $this->authToken);
        } else if ($this->authKey) {
            $headers['Authorization'] = sprintf('key %s', $this->authKey);
        }

        return $headers;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param mixed $body
     * @param array $headers
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
        $this->logger->debug(sprintf('DeskPROClient: %s %s', $method, $url), [
            'headers' => $headers,
            'body'    => $body
        ]);
        
        return new Request($method, $url, $headers, $body);
    }

    /**
     * @param string $body
     * @return Response|mixed
     */
    protected function makeResponse($body)
    {
        $decoded = json_decode($body, true);
        if (!$decoded) {
            return $body;
        }

        return new Response($decoded['data'], $decoded['meta'], $decoded['linked']);
    }

    /**
     * @param string $body
     * @return Exception\APIException
     */
    protected function makeException($body)
    {
        $body = json_decode($body, true);
        if ($body === false) {
            return new Exception\MalformedResponseException('Could not JSON decode API response.');
        }

        switch($body['status']) {
            case 401:
                return new Exception\AuthenticationException($body['message']);
                break;
            case 403:
                return new Exception\AccessDeniedException($body['message']);
                break;
            case 404:
                return new Exception\NotFoundException($body['message']);
                break;
        }

        return new Exception\APIException($body['message']);
    }
}
