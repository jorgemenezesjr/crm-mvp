<?= $this->extend('layouts/admin_view') ?>

<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestão de Clientes</h2>
    <a href="<?= site_url('admin/clientes/novo') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Cliente
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clientes) && is_array($clientes)): ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= $cliente['id'] ?></td>
                                <td><strong><?= esc($cliente['nome']) ?></strong></td>
                                <td><?= esc($cliente['email']) ?></td>
                                <td>
                                    <span class="badge <?= $cliente['status'] == 'ativo' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($cliente['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('admin/clientes/editar/' . $cliente['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
    
                                    <a href="<?= site_url('admin/clientes/excluir/' . $cliente['id']) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                                        <i class="fas fa-trash"></i> Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Nenhum cliente encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>