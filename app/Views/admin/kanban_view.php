<?= $this->extend('layouts/admin_view') ?> 
<?= $this->section('conteudo') ?>
<div class="container-fluid py-4">
    <div id="kanban-container" data-url="<?= site_url('admin/clientes/updateStatus') ?>">
        <div class="row flex-nowrap overflow-auto pb-3">
            
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 small fw-bold text-uppercase">Leads</h5>
                            <span class="badge bg-white text-primary shadow-sm">
                                R$ <?= number_format($totais['lead'] ?? 0, 2, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body kanban-column" id="lead">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'lead'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>">
                                    <div class="card-body p-2">
                                        <div class="fw-bold text-dark"><?= $c['nome'] ?></div>
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
                    <div class="card-header bg-warning text-dark">
                        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 small fw-bold text-uppercase text-white">Proposta</h5> 
                            <span class="badge bg-white text-warning fw-bold shadow-sm">
                                R$ <?= number_format($totais['proposta'] ?? 0, 2, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body kanban-column" id="proposta">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'proposta'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>">
                                    <div class="card-body p-2">
                                    <div class="fw-bold text-dark"><?= $c['nome'] ?></div>
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
                    <div class="card-header bg-info text-white">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 small fw-bold text-uppercase">Negociação</h5>
                            <span class="badge bg-white text-info shadow-sm">
                                R$ <?= number_format($totais['negociacao'] ?? 0, 2, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body kanban-column" id="negociacao">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'negociacao'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>">
                                    <div class="card-body p-2">
                                        <div class="fw-bold text-dark"><?= $c['nome'] ?></div>
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
                    <div class="card-header bg-success text-white">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 small fw-bold text-uppercase">Fechado</h5>
                        <span class="badge bg-white text-success shadow-sm">
                            R$ <?= number_format($totais['fechado'] ?? 0, 2, ',', '.') ?>
                        </span>
                    </div>
                    </div>
                    <div class="card-body kanban-column" id="fechado">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'fechado'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>">
                                    <div class="card-body p-2">
                                    <div class="fw-bold text-dark"><?= $c['nome'] ?></div>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/kanban.js') ?>"></script>
<?= $this->endSection() ?>