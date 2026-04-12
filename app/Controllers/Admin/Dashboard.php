<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // auth()->user() pega os dados de quem está logado
        $userName = auth()->user()->username;

        return "
            <h1>Painel do CRM</h1>
            <p>Olá, <strong>{$userName}</strong>! Você está logado.</p>
            <hr>
            <ul>
                <li><a href='/admin/clientes'>Gerenciar Clientes</a></li>
                <li><a href='/logout'>Sair do sistema</a></li>
            </ul>
        ";
    }
}