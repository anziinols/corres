<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');
$routes->post('contact/submit', 'Home::contactSubmit');

$uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

$routes->group('', ['filter' => 'csrf'], static function ($routes) use ($uuidPattern) {
    $routes->get('login', 'Admin::login');
    $routes->post('login/authenticate', 'Admin::authenticate');

    $routes->group('admin', ['filter' => 'adminauth'], static function ($routes) use ($uuidPattern) {
        $routes->get('dashboard', 'Admin::dashboard');
        $routes->post('logout', 'Admin::logout');

        $routes->get('correspondences', 'Correspondences::index');
        $routes->get('correspondences/new', 'Correspondences::new');
        $routes->post('correspondences', 'Correspondences::create');
        $routes->get('correspondences/generate-number', 'Correspondences::generateNumber');
        $routes->get('correspondences/(' . $uuidPattern . ')', 'Correspondences::show/$1');
        $routes->get('correspondences/(' . $uuidPattern . ')/edit', 'Correspondences::edit/$1');
        $routes->post('correspondences/(' . $uuidPattern . ')', 'Correspondences::update/$1');
        $routes->put('correspondences/(' . $uuidPattern . ')', 'Correspondences::update/$1');
        $routes->patch('correspondences/(' . $uuidPattern . ')', 'Correspondences::update/$1');
        $routes->delete('correspondences/(' . $uuidPattern . ')', 'Correspondences::delete/$1');
    });
});

$routes->group('dakoii', ['filter' => 'csrf'], static function ($routes) {
    $routes->get('/', 'Dakoii::login');
    $routes->post('authenticate', 'Dakoii::authenticate');

    $routes->group('', ['filter' => 'dakoiiauth'], static function ($routes) {
        $routes->get('dashboard', 'Dakoii::dashboard');
        $routes->post('logout', 'Dakoii::logout');
    });
});

$routes->group('dakoii', ['filter' => 'csrf'], static function ($routes) use ($uuidPattern) {
    $routes->get('/', 'Dakoii::login');
    $routes->post('authenticate', 'Dakoii::authenticate');

    $routes->group('', ['filter' => 'dakoiiauth'], static function ($routes) use ($uuidPattern) {
        $routes->get('dashboard', 'Dakoii::dashboard');
        $routes->post('logout', 'Dakoii::logout');
        
        $routes->get('organizations', 'Organizations::index');
        $routes->get('organizations/new', 'Organizations::new');
        $routes->post('organizations', 'Organizations::create');
        $routes->get('organizations/generate-code', 'Organizations::generateCode');
        $routes->get('organizations/(' . $uuidPattern . ')', 'Organizations::show/$1');
        $routes->get('organizations/(' . $uuidPattern . ')/edit', 'Organizations::edit/$1');
        $routes->put('organizations/(' . $uuidPattern . ')', 'Organizations::update/$1');
        $routes->patch('organizations/(' . $uuidPattern . ')', 'Organizations::update/$1');
        $routes->delete('organizations/(' . $uuidPattern . ')', 'Organizations::delete/$1');
        
        $routes->get('organizations/(' . $uuidPattern . ')/groups', 'Groups::index/$1');
        $routes->get('organizations/(' . $uuidPattern . ')/groups/new', 'Groups::new/$1');
        $routes->post('organizations/(' . $uuidPattern . ')/groups', 'Groups::create/$1');
        $routes->get('organizations/(' . $uuidPattern . ')/groups/(' . $uuidPattern . ')', 'Groups::show/$1/$2');
        $routes->get('organizations/(' . $uuidPattern . ')/groups/(' . $uuidPattern . ')/edit', 'Groups::edit/$1/$2');
        $routes->put('organizations/(' . $uuidPattern . ')/groups/(' . $uuidPattern . ')', 'Groups::update/$1/$2');
        $routes->patch('organizations/(' . $uuidPattern . ')/groups/(' . $uuidPattern . ')', 'Groups::update/$1/$2');
        $routes->delete('organizations/(' . $uuidPattern . ')/groups/(' . $uuidPattern . ')', 'Groups::delete/$1/$2');
        
        $routes->get('organizations/(' . $uuidPattern . ')/users', 'Users::index/$1');
        $routes->get('organizations/(' . $uuidPattern . ')/users/new', 'Users::new/$1');
        $routes->post('organizations/(' . $uuidPattern . ')/users', 'Users::create/$1');
        $routes->get('organizations/(' . $uuidPattern . ')/users/(' . $uuidPattern . ')', 'Users::show/$1/$2');
        $routes->get('organizations/(' . $uuidPattern . ')/users/(' . $uuidPattern . ')/edit', 'Users::edit/$1/$2');
        $routes->put('organizations/(' . $uuidPattern . ')/users/(' . $uuidPattern . ')', 'Users::update/$1/$2');
        $routes->patch('organizations/(' . $uuidPattern . ')/users/(' . $uuidPattern . ')', 'Users::update/$1/$2');
        $routes->delete('organizations/(' . $uuidPattern . ')/users/(' . $uuidPattern . ')', 'Users::delete/$1/$2');

        $routes->get('correspondence-types', 'CorrespondenceTypes::index');
        $routes->get('correspondence-types/new', 'CorrespondenceTypes::new');
        $routes->post('correspondence-types', 'CorrespondenceTypes::create');
        $routes->get('correspondence-types/(' . $uuidPattern . ')', 'CorrespondenceTypes::show/$1');
        $routes->get('correspondence-types/(' . $uuidPattern . ')/edit', 'CorrespondenceTypes::edit/$1');
        $routes->put('correspondence-types/(' . $uuidPattern . ')', 'CorrespondenceTypes::update/$1');
        $routes->patch('correspondence-types/(' . $uuidPattern . ')', 'CorrespondenceTypes::update/$1');
        $routes->delete('correspondence-types/(' . $uuidPattern . ')', 'CorrespondenceTypes::delete/$1');
    });
});
