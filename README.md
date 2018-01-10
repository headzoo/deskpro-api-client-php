Deskpro API PHP Client
======================
PHP client for use with the Deskpro API.

* [Installing](#installing)
* [Basic Usage](#basic-usage)
* [Default Headers](#default-headers)
* [Logging](#logging)
* [Guzzle](#guzzle)
* [Testing](#testing)

## Requirements

* PHP 5.5+ with Composer
* Guzzlehttp/guzzle >= 6.3

## Installing

```
composer require deskpro/deskpro-api-client-php
```

## Basic Usage

```php
<?php
use Deskpro\API\DeskproClient;
use Deskpro\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

// Create a new client with the URL to your Deskpro instance.
$client = new DeskproClient('http://deskpro-dev.com');

// Set the ID of the user to authenticate, and either the auth
// key or token.
// $client->setAuthKey(1, 'dev-admin-code');
// $client->setAuthToken(1, 'AWJ2BQ7WG589PQ6S862TCGY4');

try {
    $resp = $client->get('/articles');
    print_r($resp->getData());
    print_r($resp->getMeta());
} catch (APIException $e) {
    echo $e->getMessage();
}
```

#### Async usage

```php
<?php
use Deskpro\API\DeskproClient;
use Deskpro\API\APIResponseInterface;
use Deskpro\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskproClient('http://deskpro-dev.com');
$client->setAuthKey(1, 'dev-admin-code');

$promise = $client->getAsync('/articles');
$promise->then(function(APIResponseInterface $resp) {
    print_r($resp->getData());
}, function(APIException $err) {
    echo $err->getMessage();
});
$promise->wait();
```

#### Posting values

```php
<?php
use Deskpro\API\DeskproClient;
use Deskpro\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskproClient('http://deskpro-dev.com');
$client->setAuthKey(1, 'dev-admin-code');

try {
    $body = [
        'title'              => 'This is a title',
        'content'            => 'This is the content',
        'content_input_type' => 'rte',
        'status'             => 'published'
    ];
    $resp = $client->post('/articles', $body);
    print_r($resp->getData());
} catch (APIException $e) {
    echo $e->getMessage();
}
```

#### Uploading a file

```php
<?php
use Deskpro\API\DeskproClient;
use Deskpro\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskproClient('http://deskpro-dev.com');
$client->setAuthKey(1, 'dev-admin-code');

try {
    $resp = $client->post('/blobs/temp', [
        'multipart' => [
            [
                'name'     => 'file',
                'filename' => 'test.gif',
                'contents' => fopen('test.gif', 'r')
            ]
        ]
    ]);
    print_r($resp->getData());
} catch (APIException $e) {
    echo $e->getMessage();
}
```

#### Interpolating URLs

```php
<?php
use Deskpro\API\DeskproClient;
use Deskpro\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskproClient('http://deskpro-dev.com');
$client->setAuthKey(1, 'dev-admin-code');

$params = [
    'id'       => 5,
    'parentId' => 101,
    'limit'    => 25,
    'offset'   => 100
];

// The params are interplated into the endpoint URL so it becomes:
// "/articles/101/5?limit=25&offset=100"
$client->get('/articles/{parentId}/{id}', $params);
```


#### Array access

The APIResponseInterface interface implements [ArrayAccess](http://php.net/manual/en/class.arrayaccess.php), 
[Iterator](http://php.net/manual/en/class.iterator.php), and [Countable](http://php.net/manual/en/class.countable.php).
Only applicable when the API returns an array.

```php
<?php
use Deskpro\API\DeskproClient;
use Deskpro\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskproClient('http://deskpro-dev.com');
$client->setAuthKey(1, 'dev-admin-code');

try {
    $resp = $client->get('/articles');
    echo "Found " . count($resp) . " articles\n";
    foreach($resp as $article) {
        echo $article['title'] . "\n";
    }
} catch (APIException $e) {
    echo $e->getMessage();
}
```


## Default Headers
Custom headers may be sent with each request by passing them to the `setDefaultHeaders()` method.

```php
<?php
use Deskpro\API\DeskproClient;
use Deskpro\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskproClient('http://deskpro-dev.com');
$client->setAuthKey(1, 'dev-admin-code');
$client->setDefaultHeaders([
    'X-Custom-Value' => 'foo'
]);

try {
    $resp = $client->get('/articles');
    print_r($resp->getData());
} catch (APIException $e) {
    echo $e->getMessage();
}
```


## Logging
Requests may be logged by providing an instance of `Psr\Log\LoggerInterface` to the `setLogger()` method.

```php
<?php
use Deskpro\API\DeskproClient;
use Deskpro\API\Exception\APIException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskproClient('http://deskpro-dev.com');
$client->setAuthKey(1, 'dev-admin-code');

$log = new Logger('name');
$log->pushHandler(new StreamHandler('path/to/your.log', Logger::DEBUG));
$client->setLogger($log);

try {
    $resp = $client->get('/articles');
    print_r($resp->getData());
} catch (APIException $e) {
    echo $e->getMessage();
}
```


## Guzzle
Guzzle is used to make HTTP requests. A default Guzzle client will be used unless one is provided.

```php
<?php
use Deskpro\API\DeskproClient;
use GuzzleHttp\Client;

include(__DIR__ . '/vendor/autoload.php');

$httpClient = new Client([
    'timeout' => 60
]);
$client = new DeskproClient('http://deskpro-dev.com', $httpClient);

// Or use the setter method.
// $client->setHTTPClient($guzzle);
```

For debugging purposes, the RequestInterface, ResponseInterface, and RequestException generated by the last operation may be retrieved for inspection.

```php
<?php
use Deskpro\API\DeskproClient;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskproClient('http://deskpro-dev.com');
$client->get('/articles');

print_r($client->getLastHTTPRequest());
print_r($client->getLastHTTPResponse());
print_r($client->getLastHTTPRequestException());
```


## Testing
The composer "test" script runs the PHPUnit tests.

```
composer test
```