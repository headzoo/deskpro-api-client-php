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

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Makes requests to the Deskpro API.
 */
interface DeskproClientInterface extends LoggerAwareInterface
{
    /**
     * Returns the base URL to the DeskPRO instance
     *
     * @return string
     */
    public function getHelpdeskUrl();

    /**
     * Sets the base URL to the DeskPRO instance
     *
     * @param string $helpdeskUrl The base URL to the DeskPRO instance
     *
     * @return $this
     */
    public function setHelpdeskUrl($helpdeskUrl);

    /**
     * Returns the HTTP client used to make requests
     *
     * @return ClientInterface
     */
    public function getHTTPClient();

    /**
     * Sets the HTTP client used to make requests
     *
     * @param ClientInterface $httpClient HTTP client used to make requests
     *
     * @return $this
     */
    public function setHTTPClient(ClientInterface $httpClient);

    /**
     * Returns the object used to interpolate URLs
     *
     * @return URLInterpolatorInterface
     */
    public function getURLInterpolator();

    /**
     * Sets the object which will be used to interpolate URLs
     *
     * @param URLInterpolatorInterface $urlInterpolator The interpolator object
     * @return $this
     */
    public function setURLInterpolator(URLInterpolatorInterface $urlInterpolator);

    /**
     * Sets the person ID and authentication token
     *
     * @param int $personId The ID of the person being authenticated
     * @param string $token The authentication token
     *
     * @return $this
     */
    public function setAuthToken($personId, $token);

    /**
     * Sets the person ID and authentication key
     *
     * @param int $personId The ID of the person being authenticated
     * @param string $key The authentication key
     *
     * @return $this
     */
    public function setAuthKey($personId, $key);

    /**
     * Returns the headers sent with each request
     *
     * @return array
     */
    public function getDefaultHeaders();

    /**
     * Sets the headers sent with each request
     *
     * @param array $defaultHeaders The headers to send
     *
     * @return $this
     */
    public function setDefaultHeaders(array $defaultHeaders);

    /**
     * Returns the request used during the last operation
     *
     * Used to debug the underlying HTTP request.
     *
     * @return RequestInterface
     */
    public function getLastHTTPRequest();

    /**
     * Returns the response received from the last operation
     *
     * Used to debug the underlying HTTP request.
     *
     * @return ResponseInterface
     */
    public function getLastHTTPResponse();

    /**
     * Returns any exception created during the last operation
     *
     * Used to debug the underlying HTTP request.
     *
     * @return RequestException
     */
    public function getLastHTTPRequestException();

    /**
     * Sends a GET request to the API
     *
     * @param string $endpoint The API endpoint (path)
     * @param array $params Query and placeholder params
     *
     * @return APIResponseInterface
     * @throws Exception\APIException
     */
    public function get($endpoint, array $params = []);

    /**
     * Sends an asynchronous GET request to the API
     *
     * @param string $endpoint The API endpoint (path)
     * @param array $params Query and placeholder params
     *
     * @return PromiseInterface
     */
    public function getAsync($endpoint, array $params = []);

    /**
     * Sends a POST request to the API
     *
     * @param string $endpoint The API endpoint (path)
     * @param mixed $body Values sent in the request body
     * @param array $params Query and placeholder params
     *
     * @return APIResponseInterface
     * @throws Exception\APIException
     */
    public function post($endpoint, $body = null, array $params = []);

    /**
     * Sends an asynchronous POST request to the API
     *
     * @param string $endpoint The API endpoint (path)
     * @param mixed $body Values sent in the request body
     * @param array $params Query and placeholder params
     *
     * @return PromiseInterface
     */
    public function postAsync($endpoint, $body = null, array $params = []);

    /**
     * Sends a PUT request to the API
     *
     * @param string $endpoint The API endpoint (path)
     * @param mixed $body Values sent in the request body
     * @param array $params Query and placeholder params
     *
     * @return APIResponseInterface
     * @throws Exception\APIException
     */
    public function put($endpoint, $body = null, array $params = []);

    /**
     * Sends an asynchronous PUT request to the API
     *
     * @param string $endpoint The API endpoint (path)
     * @param mixed $body Values sent in the request body
     * @param array $params Query and placeholder params
     *
     * @return PromiseInterface
     */
    public function putAsync($endpoint, $body = null, array $params = []);

    /**
     * Sends a DELETE request to the API
     *
     * @param string $endpoint The API endpoint (path)
     * @param array $params Query and placeholder params
     *
     * @return APIResponseInterface
     * @throws Exception\APIException
     */
    public function delete($endpoint, array $params = []);

    /**
     * Sends an asynchronous DELETE request to the API
     *
     * @param string $endpoint The API endpoint (path)
     * @param array $params Query and placeholder params
     *
     * @return PromiseInterface
     */
    public function deleteAsync($endpoint, array $params = []);

    /**
     * Sends a request to the API
     *
     * @param string $method The HTTP method to use, e.g. 'GET', 'POST', etc
     * @param string $endpoint The API endpoint (path)
     * @param mixed $body Values sent in the request body
     * @param array $params Query and placeholder params
     * @param array $headers Additional headers to send with the request
     *
     * @return APIResponseInterface
     * @throws Exception\APIException
     */
    public function request($method, $endpoint, $body = null, array $params = [], array $headers = []);

    /**
     * Sends an asynchronous request to the API
     *
     * @param string $method The HTTP method to use, e.g. 'GET', 'POST', etc
     * @param string $endpoint The API endpoint (path)
     * @param mixed $body Values sent in the request body
     * @param array $params Query and placeholder params
     * @param array $headers Additional headers to send with the request
     *
     * @return PromiseInterface
     */
    public function requestAsync($method, $endpoint, $body = null, array $params = [], array $headers = []);
}