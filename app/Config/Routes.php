<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Admin Portal Routes
$routes->group('', ['filter' => 'csrf'], static function ($routes) {
    // Login routes (no auth required)
    $routes->get('login', 'Admin::login');
    $routes->post('login/authenticate', 'Admin::authenticate');

    // Protected routes (auth required)
    $routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
        $routes->get('dashboard', 'Admin::dashboard');
        $routes->post('logout', 'Admin::logout');

        // Correspondences Management (RESTful routes)
        $routes->get('correspondences', 'Correspondences::index');
        $routes->get('correspondences/new', 'Correspondences::new');
        $routes->post('correspondences', 'Correspondences::create');
        $routes->get('correspondences/generate-number', 'Correspondences::generateNumber');
        $routes->get('correspondences/(:num)', 'Correspondences::show/$1');
        $routes->get('correspondences/(:num)/edit', 'Correspondences::edit/$1');
        $routes->put('correspondences/(:num)', 'Correspondences::update/$1');
        $routes->patch('correspondences/(:num)', 'Correspondences::update/$1');
        $routes->delete('correspondences/(:num)', 'Correspondences::delete/$1');
    });
});

// Dakoii Admin Portal Routes
$routes->group('dakoii', ['filter' => 'csrf'], static function ($routes) {
    // Login routes (no auth required)
    $routes->get('/', 'Dakoii::login');
    $routes->post('authenticate', 'Dakoii::authenticate');

    // Protected routes (auth required)
    $routes->group('', ['filter' => 'dakoiiauth'], static function ($routes) {
        $routes->get('dashboard', 'Dakoii::dashboard');
        $routes->post('logout', 'Dakoii::logout');
        
        // Organizations Management (RESTful routes)
        $routes->get('organizations', 'Organizations::index');
        $routes->get('organizations/new', 'Organizations::new');
        $routes->post('organizations', 'Organizations::create');
        $routes->get('organizations/(:num)', 'Organizations::show/$1');
        $routes->get('organizations/(:num)/edit', 'Organizations::edit/$1');
        $routes->put('organizations/(:num)', 'Organizations::update/$1');
        $routes->patch('organizations/(:num)', 'Organizations::update/$1');
        $routes->delete('organizations/(:num)', 'Organizations::delete/$1');
        $routes->get('organizations/generate-code', 'Organizations::generateCode');
        
        // Groups Management (RESTful routes - nested under organizations)
        $routes->get('organizations/(:num)/groups', 'Groups::index/$1');
        $routes->get('organizations/(:num)/groups/new', 'Groups::new/$1');
        $routes->post('organizations/(:num)/groups', 'Groups::create/$1');
        $routes->get('organizations/(:num)/groups/(:num)', 'Groups::show/$1/$2');
        $routes->get('organizations/(:num)/groups/(:num)/edit', 'Groups::edit/$1/$2');
        $routes->put('organizations/(:num)/groups/(:num)', 'Groups::update/$1/$2');
        $routes->patch('organizations/(:num)/groups/(:num)', 'Groups::update/$1/$2');
        $routes->delete('organizations/(:num)/groups/(:num)', 'Groups::delete/$1/$2');
        
        // Users Management (RESTful routes - nested under organizations)
        $routes->get('organizations/(:num)/users', 'Users::index/$1');
        $routes->get('organizations/(:num)/users/new', 'Users::new/$1');
        $routes->post('organizations/(:num)/users', 'Users::create/$1');
        $routes->get('organizations/(:num)/users/(:num)', 'Users::show/$1/$2');
        $routes->get('organizations/(:num)/users/(:num)/edit', 'Users::edit/$1/$2');
        $routes->put('organizations/(:num)/users/(:num)', 'Users::update/$1/$2');
        $routes->patch('organizations/(:num)/users/(:num)', 'Users::update/$1/$2');
        $routes->delete('organizations/(:num)/users/(:num)', 'Users::delete/$1/$2');

        // Correspondence Types Management (RESTful routes)
        $routes->get('correspondence-types', 'CorrespondenceTypes::index');
        $routes->get('correspondence-types/new', 'CorrespondenceTypes::new');
        $routes->post('correspondence-types', 'CorrespondenceTypes::create');
        $routes->get('correspondence-types/(:num)', 'CorrespondenceTypes::show/$1');
        $routes->get('correspondence-types/(:num)/edit', 'CorrespondenceTypes::edit/$1');
        $routes->put('correspondence-types/(:num)', 'CorrespondenceTypes::update/$1');
        $routes->patch('correspondence-types/(:num)', 'CorrespondenceTypes::update/$1');
        $routes->delete('correspondence-types/(:num)', 'CorrespondenceTypes::delete/$1');
    });
});
