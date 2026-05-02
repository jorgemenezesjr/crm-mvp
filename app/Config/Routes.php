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
    // Dashboard
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Kanban e Listagem
    $routes->get('clientes', 'Admin\Clients::index'); 
    $routes->get('clientes/kanban', 'Admin\Clients::kanban');

    // Operações de Cadastro
    $routes->get('clientes/novo', 'Admin\Clients::create'); 
    $routes->post('clientes/salvar', 'Admin\Clients::store');
    $routes->get('clientes/editar/(:num)', 'Admin\Clients::edit/$1');
    $routes->post('clientes/atualizar/(:num)', 'Admin\Clients::update/$1');
    $routes->get('clientes/excluir/(:num)', 'Admin\Clients::delete/$1');

    // Lógica do Kanban (Ajax)
    $routes->post('clientes/updateStatus', 'Admin\Clients::updateStatus');
    $routes->post('clientes/finalizar', 'Admin\Clients::finalizar'); 

    // Notas e Agendamentos
    $routes->post('clientes/addNota', 'Admin\Clients::addNota');
    $routes->post('clientes/setNextStep', 'Admin\Clients::setNextStep');
    $routes->post('clientes/completeNextStep', 'Admin\Clients::completeNextStep');
    
    // Histórico
    $routes->get('clientes/historico/(:num)', 'Admin\Clients::historico/$1');
    
    
});

service('auth')->routes($routes);
