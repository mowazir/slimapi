<?php

declare(strict_types=1);

use App\Repositories\ProductRepository;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestResponseArgs;


define('APP_ROOT', dirname(__DIR__));

require_once APP_ROOT . '/vendor/autoload.php';

$builder = new ContainerBuilder;

$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();

$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$app->get('/api/products', function(Request $request, Response $response) {

    $repository = $this->get(ProductRepository::class);

    $data = $repository->getAll();

    $body = json_encode($data);

    $response->getBody()->write($body);

    return $response->withHeader('Content-Type', 'application/json');

});

$app->get('/api/products/{id:[0-9]+}', function(Request $request, Response $response, string $id){

    $repository =  $this->get(App\Repositories\ProductRepository::class);

    $product = $repository->getById((int) $id);

    if($product === false) {
        throw new \Slim\Exception\HttpNotFoundException($request, message: 'product not found');
        
    }

    $body = json_encode($product);

    $response->getBody()->write($body);

    return $response->withHeader('Content-Type', 'application/json');

});


$app->run();



