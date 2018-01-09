DeskPRO API PHP Client
======================

## Installing

```
composer require deskpro-api-client-php
```

## Basic usage

```php
<?php
use DeskPRO\API\DeskPROClient;
use DeskPRO\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskPROClient('http://deskpro-dev.com');
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

Async usage:

```php
<?php
use DeskPRO\API\DeskPROClient;
use DeskPRO\API\Response;
use DeskPRO\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskPROClient('http://deskpro-dev.com');
$client->setAuthKey(1, 'dev-admin-code');

$promise = $client->getAsync('/articles');
$promise->then(function(Response $resp) {
    print_r($resp->getData());
}, function(APIException $err) {
    echo $err->getMessage();
});
$promise->wait();
```

Posting values:

```php
<?php
use DeskPRO\API\DeskPROClient;
use DeskPRO\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskPROClient('http://deskpro-dev.com');
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

Uploading a file:

```php
<?php
use DeskPRO\API\DeskPROClient;
use DeskPRO\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskPROClient('http://deskpro-dev.com');
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

Adding default headers:

```php
<?php
use DeskPRO\API\DeskPROClient;
use DeskPRO\API\Exception\APIException;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskPROClient('http://deskpro-dev.com');
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

Logging:

```php
<?php
use DeskPRO\API\DeskPROClient;
use DeskPRO\API\Exception\APIException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskPROClient('http://deskpro-dev.com');
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


## Customizing Guzzle

```php
<?php
use DeskPRO\API\DeskPROClient;
use GuzzleHttp\Client;

include(__DIR__ . '/vendor/autoload.php');

$httpClient = new Client([
    'timeout' => 60
]);
$client = new DeskPROClient('http://deskpro-dev.com', $httpClient);

// or use the setter method.
// $client->setHTTPClient($guzzle);
```
