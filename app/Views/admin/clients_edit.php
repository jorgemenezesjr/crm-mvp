<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Editar Cliente: <?= $cliente['nome'] ?></h4>
            </div>
            <div class="card-body">
                <form action="/admin/clientes/atualizar/<?= $cliente['id'] ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="nome" class="form-control" value="<?= $cliente['nome'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" value="<?= $cliente['email'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control" value="<?= $cliente['telefone'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="ativo" <?= $cliente['status'] == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inativo" <?= $cliente['status'] == 'inativo' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        <a href="/admin/clientes" class="btn btn-light">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>