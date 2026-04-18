<?= $this->extend('layouts/admin_view') ?> 
<?= $this->section('conteudo') ?>
<div class="container-fluid py-4">
    <div id="kanban-container" data-url="<?= site_url('admin/clientes/updateStatus') ?>">
        <div class="row flex-nowrap overflow-auto pb-3">
            
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 small fw-bold text-uppercase">Leads</h5>
                    </div>
                    <div class="card-body kanban-column" id="lead">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'lead'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>">
                                    <div class="card-body p-2">
                                        <div class="fw-bold text-dark"><?= $c['nome'] ?></div>
                                        <div class="small text-muted"><?= $c['telefone'] ?></div>
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
                        <h5 class="mb-0 small fw-bold text-uppercase">Proposta</h5>
                    </div>
                    <div class="card-body kanban-column" id="proposta">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'proposta'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>">
                                    <div class="card-body p-2">
                                        <div class="fw-bold text-dark"><?= $c['nome'] ?></div>
                                        <div class="small text-muted"><?= $c['telefone'] ?></div>
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
                        <h5 class="mb-0 small fw-bold text-uppercase">Negociação</h5>
                    </div>
                    <div class="card-body kanban-column" id="negociacao">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'negociacao'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>">
                                    <div class="card-body p-2">
                                        <div class="fw-bold text-dark"><?= $c['nome'] ?></div>
                                        <div class="small text-muted"><?= $c['telefone'] ?></div>
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
                        <h5 class="mb-0 small fw-bold text-uppercase">Fechado</h5>
                    </div>
                    <div class="card-body kanban-column" id="fechado">
                        <?php foreach ($clientes as $c): ?>
                            <?php if ($c['status'] == 'fechado'): ?>
                                <div class="card mb-2 shadow-sm draggable" id="client-<?= $c['id'] ?>">
                                    <div class="card-body p-2">
                                        <div class="fw-bold text-dark"><?= $c['nome'] ?></div>
                                        <div class="small text-muted"><?= $c['telefone'] ?></div>
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