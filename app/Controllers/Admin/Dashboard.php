<?php

namespace App\Controllers\Admin; // Verifique se o "A" de Admin está maiúsculo

use App\Controllers\BaseController;
use App\Models\ClientModel;

class Dashboard extends BaseController // O nome da classe deve ser igual ao arquivo
{
    public function index()
    {
        $model = new ClientModel();

        $data = [
            'titulo'          => 'Dashboard',
            'totalClientes'   => $model->countAllResults(),
            'clientesAtivos'  => $model->where('status', 'ativo')->countAllResults(),
            'ultimosClientes' => $model->orderBy('id', 'DESC')->findAll(5),
        ];

        return view('admin/dashboard_view', $data);
    }
}