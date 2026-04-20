<?= $this->extend('layouts/admin_view') ?> 
<?= $this->section('conteudo') ?>
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
<div class="modal fade" id="modalHistorico" tabindex="-1" aria-labelledby="modalHistoricoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalHistoricoLabel"><i class="fas fa-history me-2"></i>Histórico do Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historico-carregando" class="text-center d-none py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Buscando registros...</p>
                </div>
                <div class="timeline" id="timeline-historico">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

</div> </div> <div class="modal fade" id="modalHistorico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistoricoLabel">Histórico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historico-carregando" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div id="timeline-historico"></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/kanban.js') ?>"></script>
<?= $this->endSection() ?>