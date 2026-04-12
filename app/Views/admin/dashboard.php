<?= $this->extend('layouts/admin') ?>

<?= $this->section('conteudo') ?>
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase small">Total de Clientes</h6>
                    <h2 class="display-4 fw-bold mb-0"><?= $totalClientes ?></h2>
                </div>
                <i class="fas fa-users fa-3x opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase small">Clientes Ativos</h6>
                    <h2 class="display-4 fw-bold mb-0"><?= $clientesAtivos ?></h2>
                </div>
                <i class="fas fa-user-check fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold text-primary">Últimos Cadastros</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ultimosClientes as $cliente): ?>
                        <tr>
                            <td>
                                <a href="/admin/clientes/editar/<?= $cliente['id'] ?>" class="text-decoration-none fw-bold">
                                    <?= $cliente['nome'] ?>
                            </a>
</td>
                            <td><?= $cliente['email'] ?></td>
                            <td><?= date('d/m/Y', strtotime($cliente['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>