<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', function() {
    if (auth()->loggedIn()) {
        return redirect()->to('/admin/dashboard');
    }
    return redirect()->to('/login');
});


$routes->group('admin', ['filter' => 'session'], function ($routes) {
    $routes->get('dashboard', '\App\Controllers\Admin\Dashboard::index');
    $routes->get('clientes', '\App\Controllers\Admin\Clients::index'); 
    $routes->get('clientes/novo', '\App\Controllers\Admin\Clients::create'); 
    $routes->post('clientes/salvar', '\App\Controllers\Admin\Clients::store');
    $routes->get('clientes/kanban', 'Admin\Clients::kanban');
    $routes->post('clientes/updateStatus', 'Admin\Clients::updateStatus');
    $routes->get('admin/dashboard', '\App\Controllers\Admin\Dashboard::index');
    
    $routes->get('clientes/historico/(:num)', 'Admin\Clients::historico/$1');
    $routes->get('clientes/editar/(:num)', '\App\Controllers\Admin\Clients::edit/$1');
    $routes->post('clientes/atualizar/(:num)', '\App\Controllers\Admin\Clients::update/$1');
    $routes->get('clientes/excluir/(:num)', '\App\Controllers\Admin\Clients::delete/$1');
    
    
});

service('auth')->routes($routes);
