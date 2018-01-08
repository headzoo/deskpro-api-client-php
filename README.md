DeskPRO API Client
==================

Getting values:

```php
<?php
use DeskPRO\API\DeskPROClient;

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskPROClient('http://deskpro-dev.com');
$client->setAuthKey(1, 'dev-admin-code');

$resp = $client->get('/articles');
print_r($resp->getData());
```

Async getting values:

```php
<?php
use DeskPRO\API\DeskPROClient;

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

include(__DIR__ . '/vendor/autoload.php');

$client = new DeskPROClient('http://deskpro-dev.com');
$client->setAuthKey(1, 'dev-admin-code');

$body = [
    'title'              => 'This is a title',
    'content'            => 'This is the content',
    'content_input_type' => 'rte',
    'status'             => 'published'
];
$resp = $client->post('/articles', $body);
print_r($resp->getData());
```