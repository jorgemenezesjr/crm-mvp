<?= $this->extend('layouts/admin') ?>

<?= $this->section('conteudo') ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4">Editar Cliente</h2>
                <a href="<?= site_url('admin/clientes') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Dados do Cliente #<?= $cliente['id'] ?></h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= site_url('admin/clientes/atualizar/' . $cliente['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" value="<?= esc($cliente['nome']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">E-mail</label>
                            <input type="email" name="email" class="form-control" value="<?= esc($cliente['email']) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Status da Conta</label>
                            <select name="status" class="form-select">
                                <option value="ativo" <?= $cliente['status'] == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                                <option value="inativo" <?= $cliente['status'] == 'inativo' ? 'selected' : '' ?>>Inativo</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>