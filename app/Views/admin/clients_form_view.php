<?= $this->extend('layouts/admin_view') ?>

<?= $this->section('title') ?> Novo Cliente <?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
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
                    <label class="form-label fw-bold">Nome Completo</label>
                    <input type="text" name="nome" class="form-control" 
                           value="<?= $cliente['nome'] ?? '' ?>" 
                           placeholder="Digite o nome completo. Exemplo: João da Silva" required>
                </div>

                   <div class="mb-3">
                        <label class="form-label fw-bold">E-mail</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= $cliente['email'] ?? '' ?>" 
                               placeholder="exemplo@email.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Telefone</label>
                        <input type="text" name="telefone" id="telefone" class="form-control phone-mask" 
                               value="<?= $cliente['telefone'] ?? '' ?>" 
                               placeholder="(00) 00000-0000">
                    </div>

                    <div class="mb-3">
                        <label for="valor" class="form-label fw-bold">Valor Estimado (R$)</label>
                        <input type="text" 
                               name="valor" 
                               id="valor" 
                               class="form-control money" 
                               value="<?= (isset($cliente['valor']) && $cliente['valor'] > 0) ? number_format($cliente['valor'], 2, ',', '.') : '' ?>" 
                               placeholder="0,00">
                        <small class="text-muted">Valor da proposta ou potencial de venda.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Etapa do Funil (Status)</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="lead"       <?= (isset($cliente) && $cliente['status'] == 'lead') ? 'selected' : '' ?>>Lead (Novo)</option>
                            <option value="proposta"   <?= (isset($cliente) && $cliente['status'] == 'proposta') ? 'selected' : '' ?>>Proposta Enviada</option>
                            <option value="negociacao" <?= (isset($cliente) && $cliente['status'] == 'negociacao') ? 'selected' : '' ?>>Em Negociação</option>
                            <option value="fechado"    <?= (isset($cliente) && $cliente['status'] == 'fechado') ? 'selected' : '' ?>>Contrato Fechado (Ganhamos!)</option>
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
<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/clients.js') ?>"></script>
<?= $this->endSection() ?>