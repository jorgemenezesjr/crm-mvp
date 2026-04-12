<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?> Novo Cliente <?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Cadastrar Novo Cliente</h4>
            </div>
            <div class="card-body">
                <form action="/admin/clientes/salvar" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: João Silva" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="joao@email.com">
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone / WhatsApp</label>
                        <input type="text" name="telefone" id="telefone" class="form-control" placeholder="(11) 99999-9999">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status do Cliente</label>
                        <select name="status" id="status" class="form-select">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">Salvar Cliente</button>
                        <a href="/admin/clientes" class="btn btn-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>