<?php

require_once('./config/config.php');

use App\Controller\ContractController;
use Core\Container\Container;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

$container = Container::getInstance();

/** @var ContractController $controller */
$controller = $container->getService('App\\Controller\\ContractController');

$request = ServerRequest::fromGlobals();

/** @var ResponseInterface $response */
$response = $controller->getContacts($request);

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header($name . ': ' . $value, false);
    }
}
http_response_code((int)$response->getStatusCode());
echo $response->getBody();
