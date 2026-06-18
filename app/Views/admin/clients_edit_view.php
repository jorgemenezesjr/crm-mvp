<?= $this->extend('layouts/admin_view') ?>

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

                        <div class="mb-3">
                            <label for="valor" class="form-label fw-bold">Valor Estimado (R$)</label>
                            <input type="text" 
                                   name="valor" 
                                   id="valor" 
                                   class="form-control money" 
                                   value="<?= isset($cliente) ? number_format($cliente['valor'], 2, ',', '.') : '0,00' ?>" 
                                   placeholder="0,00">
                            <small class="text-muted">Valor da proposta ou potencial de venda.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="origem" class="form-label fw-bold">Origem do Lead</label>
                            <select class="form-select" id="origem" name="origem">
                                <option value="Não Informado" <?= (isset($cliente['origem']) && $cliente['origem'] == 'Não Informado') ? 'selected' : '' ?>>Selecione a origem...</option>
                                <option value="Instagram" <?= (isset($cliente['origem']) && $cliente['origem'] == 'Instagram') ? 'selected' : '' ?>>Instagram</option>
                                <option value="Google" <?= (isset($cliente['origem']) && $cliente['origem'] == 'Google') ? 'selected' : '' ?>>Google (Pesquisa/Tráfego Pago)</option>
                                <option value="Facebook" <?= (isset($cliente['origem']) && $cliente['origem'] == 'Facebook') ? 'selected' : '' ?>>Facebook</option>
                                <option value="WhatsApp" <?= (isset($cliente['origem']) && $cliente['origem'] == 'WhatsApp') ? 'selected' : '' ?>>WhatsApp / Prospecção Direta</option>
                                <option value="Indicação" <?= (isset($cliente['origem']) && $cliente['origem'] == 'Indicação') ? 'selected' : '' ?>>Indicação de Cliente</option>
                            </select>
                            <div class="form-text">Identificar de onde o cliente veio ajuda a mensurar o retorno de marketing.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">Etapa do Funil (Status)</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="lead"       <?= (isset($cliente) && $cliente['status'] == 'lead') ? 'selected' : '' ?>>Lead (Novo)</option>
                                <option value="proposta"   <?= (isset($cliente) && $cliente['status'] == 'proposta') ? 'selected' : '' ?>>Proposta Enviada</option>
                                <option value="negociacao" <?= (isset($cliente) && $cliente['status'] == 'negociacao') ? 'selected' : '' ?>>Em Negociação</option>
                                <option value="fechado"    <?= (isset($cliente) && $cliente['status'] == 'fechado') ? 'selected' : '' ?>>Contrato Fechado (Ganhamos!)</option>
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
<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/clients.js') ?>"></script>
<?= $this->endSection() ?>