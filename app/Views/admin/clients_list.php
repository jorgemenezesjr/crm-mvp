<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?> Lista de Clientes <?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Clientes Cadastrados</h2>
    <a href="/admin/clientes/novo" class="btn btn-primary">+ Novo Cliente</a>
</div>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= $cliente['id'] ?></td>
                    <td><?= $cliente['nome'] ?></td>
                    <td><?= $cliente['email'] ?></td>
                    <td><?= $cliente['telefone'] ?></td>
                    <td>
                        <span class="badge bg-<?= $cliente['status'] == 'ativo' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($cliente['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="/admin/clientes/editar/<?= $cliente['id'] ?>" class="btn btn-sm btn-warning">Editar</a>

                        <a href="/admin/clientes/excluir/<?= $cliente['id'] ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.')">
                           Excluir
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>