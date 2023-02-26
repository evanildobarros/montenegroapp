<?php
/**
 * @var \Cake\Routing\RouteBuilder $routes
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;

$routes->setRouteClass(DashedRoute::class);

$routes->scope('/', function (RouteBuilder $builder) {
    $builder->setExtensions(['json', 'pdf']);
    $builder->connect('/', [
        'controller' => 'Pages',
        'action' => 'home',
    ]);
    $builder->connect('/termos', [
        'controller' => 'Pages',
        'action' => 'termos',
    ]);
    $builder->connect('/.well-known/jwks', [
        'controller' => 'Jwks',
        'action' => 'index',
    ]);
    $builder->fallbacks(DashedRoute::class);
});

$routes->prefix('Admin', function (RouteBuilder $builder) {
    $builder->connect('/', [
        'controller' => 'Users',
        'action' => 'dashboard',
    ]);
    $builder->setExtensions(['json']);
    $builder->fallbacks(DashedRoute::class);
});

$routes->prefix('Api', function (RouteBuilder $builder) {
    $builder->setExtensions(['json']);

    $apiResources = [
        'Estados',
    ];
    foreach ($apiResources as $apiResource) {
        $builder->resources($apiResource, [
            'inflect' => 'dasherize',
        ]);
    }

    $builder->fallbacks(InflectedRoute::class);
});
