<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Dakoii Admin Portal Routes
$routes->group('dakoii', ['filter' => 'csrf'], static function ($routes) {
    // Login routes (no auth required)
    $routes->get('/', 'Dakoii::login');
    $routes->post('authenticate', 'Dakoii::authenticate');

    // Protected routes (auth required)
    $routes->group('', ['filter' => 'dakoiiauth'], static function ($routes) {
        $routes->get('dashboard', 'Dakoii::dashboard');
        $routes->post('logout', 'Dakoii::logout');
    });
});
