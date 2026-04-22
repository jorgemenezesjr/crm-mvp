<?= $this->extend('layouts/admin_view') ?> 
<?= $this->section('conteudo') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/kanban-modal.css') ?>">

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" id="kanban-search" class="form-control border-start-0 ps-0" placeholder="Buscar cliente pelo nome...">
            </div>
        </div>
    </div>
    <div id="kanban-container" data-url="<?= site_url('admin/clientes/updateStatus') ?>">
        <div class="row flex-nowrap overflow-auto pb-3">
            
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 small fw-bold text-uppercase">Leads</h5>
                        <span id="total-lead" class="badge bg-white text-primary shadow-sm">
                            R$ <?= number_format($totais['lead'] ?? 0, 2, ',', '.') ?>
                        </span>
                    </div>
                    <div class="card-body kanban-column" id="lead">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'lead'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>" data-valor="<?= $c['valor'] ?? 0 ?>">
                                    <div class="card-body p-2">
                                        <div class="fw-bold text-dark btn-historico" 
                                             data-id="<?= $c['id'] ?>" 
                                             data-nome="<?= esc($c['nome']) ?>"
                                             style="cursor: pointer;">
                                             <?= $c['nome'] ?>
                                        </div>

                                        <div class="small text-muted"><?= $c['telefone'] ?></div>
                                        <div class="mt-1 d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-success border">
                                                R$ <?= number_format($c['valor'] ?? 0, 2, ',', '.') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 small fw-bold text-uppercase">Proposta</h5> 
                        <span id="total-proposta" class="badge bg-white text-warning fw-bold shadow-sm">
                            R$ <?= number_format($totais['proposta'] ?? 0, 2, ',', '.') ?>
                        </span>
                    </div>
                    <div class="card-body kanban-column" id="proposta">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'proposta'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>" data-valor="<?= $c['valor'] ?? 0 ?>">
                                    <div class="card-body p-2">
                                        <div class="fw-bold text-dark btn-historico" 
                                             data-id="<?= $c['id'] ?>" 
                                             data-nome="<?= esc($c['nome']) ?>"
                                             style="cursor: pointer;">
                                             <?= $c['nome'] ?>
                                        </div>
                                        <div class="small text-muted"><?= $c['telefone'] ?></div>
                                        <div class="mt-1 d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-success border">
                                                R$ <?= number_format($c['valor'] ?? 0, 2, ',', '.') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 small fw-bold text-uppercase">Negociação</h5>
                        <span id="total-negociacao" class="badge bg-white text-info shadow-sm">
                            R$ <?= number_format($totais['negociacao'] ?? 0, 2, ',', '.') ?>
                        </span>
                    </div>
                    <div class="card-body kanban-column" id="negociacao">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'negociacao'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>" data-valor="<?= $c['valor'] ?? 0 ?>">
                                    <div class="card-body p-2">
                                        <div class="fw-bold text-dark btn-historico" 
                                             data-id="<?= $c['id'] ?>" 
                                             data-nome="<?= esc($c['nome']) ?>"
                                             style="cursor: pointer;">
                                             <?= $c['nome'] ?>
                                        </div>
                                        <div class="small text-muted"><?= $c['telefone'] ?></div>
                                        <div class="mt-1 d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-success border">
                                                R$ <?= number_format($c['valor'] ?? 0, 2, ',', '.') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 small fw-bold text-uppercase">Fechado</h5>
                        <span id="total-fechado" class="badge bg-white text-success shadow-sm">
                            R$ <?= number_format($totais['fechado'] ?? 0, 2, ',', '.') ?>
                        </span>
                    </div>
                    <div class="card-body kanban-column" id="fechado">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'fechado'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>" data-valor="<?= $c['valor'] ?? 0 ?>">
                                    <div class="card-body p-2">
                                        <div class="fw-bold text-dark btn-historico" 
                                             data-id="<?= $c['id'] ?>" 
                                             data-nome="<?= esc($c['nome']) ?>"
                                             style="cursor: pointer;">
                                             <?= $c['nome'] ?>
                                        </div>
                                        <div class="small text-muted"><?= $c['telefone'] ?></div>
                                        <div class="mt-1 d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-success border">
                                                R$ <?= number_format($c['valor'] ?? 0, 2, ',', '.') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modalHistorico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content crm-custom">
            
            <div class="crm-header d-flex justify-content-between align-items-start">
                <div>
                    <h2 id="modal-nome-cliente" class="h4 mb-0 fw-bold text-dark">Nome do Cliente</h2>
                    <div id="modal-valor-proposta" class="crm-proposal-badge">R$ 0,00</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="crm-action-grid">
                <a href="#" id="link-call" class="crm-btn-action bg-call">
                    <i class="fas fa-phone-alt"></i> Ligar
                </a>
                <a href="#" id="link-whatsapp" target="_blank" class="crm-btn-action bg-whatsapp">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a href="#" id="link-email" class="crm-btn-action bg-email">
                    <i class="fas fa-envelope"></i> E-mail
                </a>
            </div>

            <div class="crm-body">
                <div class="note-input-container">
                    <input type="text" id="noteInput" class="note-input" placeholder="Nota rápida + Enter...">
                </div>

                <h6 class="text-uppercase fw-bold text-muted mb-3" style="font-size: 0.75rem;">Histórico Recente</h6>
                
                <div id="historico-carregando" class="text-center d-none py-3">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                </div>

                <div class="timeline-custom" id="timeline-historico">
                    </div>
            </div>
            
            <div id="next-step-container" class="mt-3 p-3 border rounded shadow-sm" style="background-color: #fffdec; border-color: #ffeeb2 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="fw-bold text-dark"><i class="fas fa-thumbtack me-1 text-warning"></i> PRÓXIMO PASSO</small>
                </div>

                <div id="display-tarefa" class="d-none">
                    <div class="d-flex align-items-start gap-2">
                        <input type="checkbox" class="form-check-input mt-1" id="check-concluir" style="cursor:pointer">
                        <div class="flex-grow-1">
                            <div id="lbl-tarefa-desc" class="fw-bold small text-dark" style="line-height: 1.2;"></div>
                            <small id="lbl-tarefa-data" class="text-muted" style="font-size: 11px;"></small>
                        </div>
                    </div>
                </div>

                <div id="form-tarefa">
                    <div class="row g-1">
                        <div class="col-8">
                            <input type="text" id="input-next-desc" class="form-control form-control-sm" placeholder="O que fazer a seguir?">
                        </div>
                        <div class="col-4">
                            <input type="date" id="input-next-date" class="form-control form-control-sm">
                        </div>
                    </div>
                    <button type="button" id="btn-save-next" class="btn btn-warning btn-sm w-100 mt-2 fw-bold" style="font-size: 11px;">
                        AGENDAR RETORNO
                    </button>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/kanban.js') ?>"></script>
<?= $this->endSection() ?>