<?= $this->extend('layouts/admin_view') ?> 
<?= $this->section('conteudo') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/kanban-modal.css') ?>">

<div class="container-fluid py-4">
    
    <!-- BARRA DE PESQUISA E FILTROS DO KANBAN -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body py-2 bg-light rounded">
            <form method="GET" action="<?= site_url('admin/clientes/kanban') ?>" class="row g-2 align-items-center">
                
                <!-- Pesquisa por Nome -->
                <div class="col-md-4 col-sm-12">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="kanban-search" class="form-control border-start-0 ps-0" placeholder="Buscar cliente pelo nome...">
                    </div>
                </div>

                <!-- Filtro por Vendedor -->
                <div class="col-md-3 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-user-tie text-muted"></i></span>
                        <select name="vendedor" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos os Vendedores</option>
                            <option value="sem_responsavel" <?= (($filtroVendedor ?? '') === 'sem_responsavel') ? 'selected' : '' ?>>Sem Responsável</option>
                            <?php if (!empty($vendedores)): ?>
                                <?php foreach ($vendedores as $v): ?>
                                    <option value="<?= $v['id'] ?>" <?= (($filtroVendedor ?? '') == $v['id']) ? 'selected' : '' ?>>
                                        <?= esc($v['username'] ?? $v['email']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Filtro por Tarefa -->
                <div class="col-md-3 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-tasks text-muted"></i></span>
                        <select name="tarefa" class="form-select" onchange="this.form.submit()">
                            <option value="">Todas as Tarefas</option>
                            <option value="atrasadas" <?= (($filtroTarefa ?? '') === 'atrasadas') ? 'selected' : '' ?>>⚠️ Tarefas Atrasadas</option>
                            <option value="hoje" <?= (($filtroTarefa ?? '') === 'hoje') ? 'selected' : '' ?>>📅 Tarefas para Hoje</option>
                            <option value="futuras" <?= (($filtroTarefa ?? '') === 'futuras') ? 'selected' : '' ?>>➡️ Tarefas Futuras</option>
                            <option value="sem_tarefa" <?= (($filtroTarefa ?? '') === 'sem_tarefa') ? 'selected' : '' ?>>❌ Sem Tarefa Agendada</option>
                        </select>
                    </div>
                </div>

                <!-- Botão Limpar Filtros -->
                <?php if (!empty($filtroVendedor) || !empty($filtroTarefa)): ?>
                    <div class="col-md-2 col-sm-12">
                        <a href="<?= site_url('admin/clientes/kanban') ?>" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i>Limpar
                        </a>
                    </div>
                <?php endif; ?>

            </form>
        </div>
    </div>

    <!-- CARDS DE METRICAS/DASHBOARD -->
    <div class="row mb-4 g-3">
        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
            <div class="card shadow-sm border-0 bg-success text-white h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase fw-bold text-white-50" style="font-size: 0.75rem;">Faturamento Realizado</small>
                        <h3 class="mb-0 fw-bold mt-1" id="dash-faturamento">
                            R$ <?= number_format($totais['fechado'] ?? 0, 2, ',', '.') ?>
                        </h3>
                    </div>
                    <div class="fs-1 text-white-50 opacity-50 ps-2">
                        <i class="fas fa-handshake"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
            <div class="card shadow-sm border-0 bg-dark text-white h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase fw-bold text-white-50" style="font-size: 0.75rem;">Pipeline em Aberto</small>
                        <h3 class="mb-0 fw-bold mt-1" id="dash-pipeline">
                            <?php 
                                $pipeline = ($totais['lead'] ?? 0) + ($totais['proposta'] ?? 0) + ($totais['negociacao'] ?? 0);
                            ?>
                            R$ <?= number_format($pipeline, 2, ',', '.') ?>
                        </h3>
                    </div>
                    <div class="fs-1 text-white-50 opacity-50 ps-2">
                        <i class="fas fa-funnel-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
            <div class="card shadow-sm border-0 bg-white text-dark h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Oportunidades Ativas</small>
                        <h3 class="mb-0 fw-bold text-primary mt-1" id="dash-ativos-qtd">
                            0
                        </h3>
                    </div>
                    <div class="fs-1 text-muted opacity-50 ps-2">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase fw-bold text-white-50" style="font-size: 0.75rem;">Taxa de Conversão</small>
                        <h3 class="mb-0 fw-bold mt-1" id="dash-conversao">
                            0%
                        </h3>
                    </div>
                    <div class="fs-1 text-white-50 opacity-50 ps-2">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>  

    <!-- KANBAN CONTAINER -->
    <div id="kanban-container" data-url="<?= site_url('admin/clientes/updateStatus') ?>">
        <div class="row flex-nowrap overflow-auto pb-3">
            
            <!-- COLUNA: LEADS -->
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
                                <?php 
                                    $hoje = date('Y-m-d');
                                    $statusTarefa = '';
                                    $badgeClass = '';

                                    if (!empty($c['next_step_at'])) {
                                        if ($c['next_step_at'] < $hoje) {
                                            $statusTarefa = 'Atrasado';
                                            $badgeClass = 'bg-danger';
                                        } elseif ($c['next_step_at'] == $hoje) {
                                            $statusTarefa = 'Hoje';
                                            $badgeClass = 'bg-warning text-dark';
                                        } else {
                                            $statusTarefa = date('d/m', strtotime($c['next_step_at']));
                                            $badgeClass = 'bg-info';
                                        }
                                    }
                                ?>

                                <div class="draggable kanban-item-container" id="client-<?= $c['id'] ?>" data-valor="<?= $c['valor'] ?? 0 ?>" data-tarefa="<?= empty($c['next_step_at']) ? 'sem-tarefa' : strtolower($statusTarefa) ?>">
                                    <?php if ($statusTarefa): ?>
                                        <span class="badge <?= $badgeClass ?> shadow-sm kanban-orelha-fixa">
                                            <i class="fas fa-calendar-check me-1"></i> <?= $statusTarefa ?>
                                        </span>
                                    <?php endif; ?>
                                    <div class="card mb-2 inner-card-visual">
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

                                            <!-- RODAPÉ DO CARD: ORIGEM E RESPONSÁVEL -->
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                <!-- Origem do Lead -->
                                                <?php
                                                    $bgOrigem = 'bg-secondary';
                                                    $iconOrigem = 'fa-globe';
                                                    $origem = mb_strtolower($c['origem'] ?? 'não informado');

                                                    if (strpos($origem, 'instagram') !== false) {
                                                        $bgOrigem = 'bg-danger';
                                                        $iconOrigem = 'fa-instagram';
                                                    } elseif (strpos($origem, 'google') !== false) {
                                                        $bgOrigem = 'bg-primary';
                                                        $iconOrigem = 'fa-google';
                                                    } elseif (strpos($origem, 'whatsapp') !== false) {
                                                        $bgOrigem = 'bg-success';
                                                        $iconOrigem = 'fa-whatsapp';
                                                    } elseif (strpos($origem, 'indicação') !== false || strpos($origem, 'indicacao') !== false) {
                                                        $bgOrigem = 'bg-warning text-dark';
                                                        $iconOrigem = 'fa-user-check';
                                                    }
                                                ?>
                                                <span class="badge <?= $bgOrigem ?> d-inline-flex align-items-center gap-1" style="font-size: 0.7rem; padding: 3px 6px;">
                                                    <i class="fab <?= $iconOrigem ?> fas"></i> 
                                                    <?= ucfirst($c['origem'] ?? 'Não Informado') ?>
                                                </span>

                                                <!-- Badge do Responsável (Por último) -->
                                                <span class="badge bg-light text-dark border rounded-pill small" title="Responsável pelo lead">
                                                    <i class="fas fa-user-circle text-primary me-1"></i>
                                                    <?= esc($c['responsable_nome'] ?? 'Sem responsável') ?>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- COLUNA: PROPOSTA -->
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
                                <?php 
                                    $hoje = date('Y-m-d');
                                    $statusTarefa = '';
                                    $badgeClass = '';

                                    if (!empty($c['next_step_at'])) {
                                        if ($c['next_step_at'] < $hoje) {
                                            $statusTarefa = 'Atrasado';
                                            $badgeClass = 'bg-danger';
                                        } elseif ($c['next_step_at'] == $hoje) {
                                            $statusTarefa = 'Hoje';
                                            $badgeClass = 'bg-warning text-dark';
                                        } else {
                                            $statusTarefa = date('d/m', strtotime($c['next_step_at']));
                                            $badgeClass = 'bg-info';
                                        }
                                    }
                                ?>
                                <div class="draggable kanban-item-container" id="client-<?= $c['id'] ?>" data-valor="<?= $c['valor'] ?? 0 ?>" data-tarefa="<?= empty($c['next_step_at']) ? 'sem-tarefa' : strtolower($statusTarefa) ?>">
                                    <?php if ($statusTarefa): ?>
                                        <span class="badge <?= $badgeClass ?> shadow-sm kanban-orelha-fixa">
                                            <i class="fas fa-calendar-check me-1"></i> <?= $statusTarefa ?>
                                        </span>
                                    <?php endif; ?>

                                    <div class="card mb-2 inner-card-visual">
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

                                            <!-- RODAPÉ DO CARD: ORIGEM E RESPONSÁVEL -->
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                <?php
                                                    $bgOrigem = 'bg-secondary';
                                                    $iconOrigem = 'fa-globe';
                                                    $origem = mb_strtolower($c['origem'] ?? 'não informado');

                                                    if (strpos($origem, 'instagram') !== false) {
                                                        $bgOrigem = 'bg-danger';
                                                        $iconOrigem = 'fa-instagram';
                                                    } elseif (strpos($origem, 'google') !== false) {
                                                        $bgOrigem = 'bg-primary';
                                                        $iconOrigem = 'fa-google';
                                                    } elseif (strpos($origem, 'whatsapp') !== false) {
                                                        $bgOrigem = 'bg-success';
                                                        $iconOrigem = 'fa-whatsapp';
                                                    } elseif (strpos($origem, 'indicação') !== false || strpos($origem, 'indicacao') !== false) {
                                                        $bgOrigem = 'bg-warning text-dark';
                                                        $iconOrigem = 'fa-user-check';
                                                    }
                                                ?>
                                                <span class="badge <?= $bgOrigem ?> d-inline-flex align-items-center gap-1" style="font-size: 0.7rem; padding: 3px 6px;">
                                                    <i class="fab <?= $iconOrigem ?> fas"></i> 
                                                    <?= ucfirst($c['origem'] ?? 'Não Informado') ?>
                                                </span>

                                                <span class="badge bg-light text-dark border rounded-pill small" title="Responsável pelo lead">
                                                    <i class="fas fa-user-circle text-primary me-1"></i>
                                                    <?= esc($c['responsable_nome'] ?? 'Sem responsável') ?>
                                                </span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- COLUNA: NEGOCIAÇÃO -->
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
                                <?php 
                                    $hoje = date('Y-m-d');
                                    $statusTarefa = '';
                                    $badgeClass = '';

                                    if (!empty($c['next_step_at']) && $c['status'] !== 'fechado') {
                                        if ($c['next_step_at'] < $hoje) {
                                            $statusTarefa = 'Atrasado';
                                            $badgeClass = 'bg-danger';
                                        } elseif ($c['next_step_at'] == $hoje) {
                                            $statusTarefa = 'Hoje';
                                            $badgeClass = 'bg-warning text-dark';
                                        } else {
                                            $statusTarefa = date('d/m', strtotime($c['next_step_at']));
                                            $badgeClass = 'bg-info';
                                        }
                                    }
                                ?>

                                <div class="draggable kanban-item-container" id="client-<?= $c['id'] ?>" data-valor="<?= $c['valor'] ?? 0 ?>" data-tarefa="<?= empty($c['next_step_at']) ? 'sem-tarefa' : strtolower($statusTarefa) ?>">
                                    <?php if ($statusTarefa): ?>
                                        <span class="badge <?= $badgeClass ?> shadow-sm kanban-orelha-fixa" style="font-size: 0.7rem;">
                                            <i class="fas fa-calendar-check me-1"></i> <?= $statusTarefa ?>
                                        </span>
                                    <?php endif; ?>
                                    <div class="card mb-2 inner-card-visual">
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

                                            <!-- RODAPÉ DO CARD: ORIGEM E RESPONSÁVEL -->
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                <?php
                                                    $bgOrigem = 'bg-secondary';
                                                    $iconOrigem = 'fa-globe';
                                                    $origem = mb_strtolower($c['origem'] ?? 'não informado');

                                                    if (strpos($origem, 'instagram') !== false) {
                                                        $bgOrigem = 'bg-danger';
                                                        $iconOrigem = 'fa-instagram';
                                                    } elseif (strpos($origem, 'google') !== false) {
                                                        $bgOrigem = 'bg-primary';
                                                        $iconOrigem = 'fa-google';
                                                    } elseif (strpos($origem, 'whatsapp') !== false) {
                                                        $bgOrigem = 'bg-success';
                                                        $iconOrigem = 'fa-whatsapp';
                                                    } elseif (strpos($origem, 'indicação') !== false || strpos($origem, 'indicacao') !== false) {
                                                        $bgOrigem = 'bg-warning text-dark';
                                                        $iconOrigem = 'fa-user-check';
                                                    }
                                                ?>
                                                <span class="badge <?= $bgOrigem ?> d-inline-flex align-items-center gap-1" style="font-size: 0.7rem; padding: 3px 6px;">
                                                    <i class="fab <?= $iconOrigem ?> fas"></i> 
                                                    <?= ucfirst($c['origem'] ?? 'Não Informado') ?>
                                                </span>

                                                <span class="badge bg-light text-dark border rounded-pill small" title="Responsável pelo lead">
                                                    <i class="fas fa-user-circle text-primary me-1"></i>
                                                    <?= esc($c['responsable_nome'] ?? 'Sem responsável') ?>
                                                </span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- COLUNA: FECHADO -->
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
                                <?php 
                                    $hoje = date('Y-m-d');
                                    $statusTarefa = '';
                                    $badgeClass = '';

                                    if (!empty($c['next_step_at'])) {
                                        if ($c['next_step_at'] < $hoje) {
                                            $statusTarefa = 'Atrasado';
                                            $badgeClass = 'bg-danger';
                                        } elseif ($c['next_step_at'] == $hoje) {
                                            $statusTarefa = 'Hoje';
                                            $badgeClass = 'bg-warning text-dark';
                                        } else {
                                            $statusTarefa = date('d/m', strtotime($c['next_step_at']));
                                            $badgeClass = 'bg-info';
                                        }
                                    }
                                ?>

                                <div class="draggable kanban-item-container" id="client-<?= $c['id'] ?>" data-valor="<?= $c['valor'] ?? 0 ?>" data-tarefa="<?= empty($c['next_step_at']) ? 'sem-tarefa' : strtolower($statusTarefa) ?>">
                                    <?php if ($statusTarefa): ?>
                                        <span class="badge <?= $badgeClass ?> shadow-sm kanban-orelha-fixa" style="font-size: 0.7rem;">
                                            <i class="fas fa-calendar-check me-1"></i> <?= $statusTarefa ?>
                                        </span>
                                    <?php endif; ?>
                                    <div class="card mb-2 inner-card-visual">
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

                                            <!-- RODAPÉ DO CARD: ORIGEM E RESPONSÁVEL -->
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                <?php
                                                    $bgOrigem = 'bg-secondary';
                                                    $iconOrigem = 'fa-globe';
                                                    $origem = mb_strtolower($c['origem'] ?? 'não informado');

                                                    if (strpos($origem, 'instagram') !== false) {
                                                        $bgOrigem = 'bg-danger';
                                                        $iconOrigem = 'fa-instagram';
                                                    } elseif (strpos($origem, 'google') !== false) {
                                                        $bgOrigem = 'bg-primary';
                                                        $iconOrigem = 'fa-google';
                                                    } elseif (strpos($origem, 'whatsapp') !== false) {
                                                        $bgOrigem = 'bg-success';
                                                        $iconOrigem = 'fa-whatsapp';
                                                    } elseif (strpos($origem, 'indicação') !== false || strpos($origem, 'indicacao') !== false) {
                                                        $bgOrigem = 'bg-warning text-dark';
                                                        $iconOrigem = 'fa-user-check';
                                                    }
                                                ?>
                                                <span class="badge <?= $bgOrigem ?> d-inline-flex align-items-center gap-1" style="font-size: 0.7rem; padding: 3px 6px;">
                                                    <i class="fab <?= $iconOrigem ?> fas"></i> 
                                                    <?= ucfirst($c['origem'] ?? 'Não Informado') ?>
                                                </span>

                                                <span class="badge bg-light text-dark border rounded-pill small" title="Responsável pelo lead">
                                                    <i class="fas fa-user-circle text-primary me-1"></i>
                                                    <?= esc($c['responsable_nome'] ?? 'Sem responsável') ?>
                                                </span>
                                            </div>
                                            
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

    <!-- DROP ZONES PARA GANHO/PERDIDO -->
    <div id="drop-zones" class="d-flex justify-content-center gap-3 d-none" style="position: fixed; bottom: 20px; left: 0; right: 0; z-index: 9999;">
        <div id="zone-success" class="drop-zone bg-success text-white p-3 rounded shadow-lg border border-2 border-light" style="min-width: 200px; text-align: center;">
            <i class="fas fa-trophy me-2"></i> SOLTE PARA GANHAR
        </div>
        <div id="zone-danger" class="drop-zone bg-danger text-white p-3 rounded shadow-lg border border-2 border-light" style="min-width: 200px; text-align: center;">
            <i class="fas fa-thumbs-down me-2"></i> SOLTE PARA PERDER
        </div>
    </div>
</div>

<!-- MODAL DE HISTÓRICO E DETALHES -->
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
                <input type="hidden" id="modal-cliente-id">

                <div class="note-input-container">
                    <input type="text" id="noteInput" class="note-input" placeholder="Nota rápida + Enter...">
                </div>

                <h6 class="text-uppercase fw-bold text-muted mb-3" style="font-size: 0.75rem;">Histórico Recente</h6>
                
                <div id="historico-carregando" class="text-center d-none py-3">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                </div>

                <div class="timeline-custom" id="timeline-historico" style="max-height: 250px; overflow-y: auto;">
                </div>

                <div id="next-step-container" class="mt-4 p-3 border rounded shadow-sm" style="background-color: #fffdec; border-color: #ffeeb2 !important;">
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
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/kanban.js') ?>"></script>
<?= $this->endSection() ?>