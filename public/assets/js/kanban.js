const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

/**
 * Lógica do Kanban - CRM
 */
document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('kanban-container');
        const endpoint  = container.getAttribute('data-url'); 
        const colunas   = document.querySelectorAll('.kanban-column');

        // 1. Configuração das Colunas
        colunas.forEach(coluna => {
            new Sortable(coluna, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'bg-light-blue',
                onStart: function () {
                    // Mostra os botões ao arrastar
                    const zones = document.getElementById('drop-zones');
                    if(zones) zones.classList.remove('d-none');
                },
                onEnd: function (evt) {
                    // Esconde os botões ao soltar
                    const zones = document.getElementById('drop-zones');
                    if(zones) zones.classList.add('d-none');

                    const clientId = evt.item.id.replace('client-', '');
                    const newStatus = evt.to.id;

                    // --- ATUALIZAÇÃO VISUAL IMEDIATA ---
                    atualizarTotaisDinamicamente();

                    // Se o destino for um botão, NÃO faz o fetch de movimentação padrão das colunas
                    if (newStatus === 'zone-success' || newStatus === 'zone-danger') {
                        return; 
                    }

                    // Se caiu em uma coluna normal, segue o fluxo padrão:
                    fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content')
                        },
                        body: JSON.stringify({ id: clientId, status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== 'success') {
                            location.reload();
                        } else {
                            Toast.fire({ icon: 'success', title: 'Status updated!' });
                            atualizarTotaisDinamicamente();
                        }
                    });
                },
            });
        });

        // 2. Lógica separada apenas para os BOTÕES (Drop Zones)
        document.querySelectorAll('.drop-zone').forEach(zona => {
            new Sortable(zona, {
                group: 'kanban',
                put: true,
                pull: false,
                onAdd: function (evt) {
                    const clientId = evt.item.id.replace('client-', '');
                    const targetZone = evt.to.id;

                    if (targetZone === 'zone-success') {
                        confirmarGanho(clientId, evt.item);
                    } else if (targetZone === 'zone-danger') {
                        confirmarPerda(clientId, evt.item);
                    }
                }
            });
        });
    
        // 3. Lógica do Filtro de Busca
        const searchInput = document.getElementById('kanban-search');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                let searchTerm = this.value.toLowerCase();
                let cards = document.querySelectorAll('.draggable');

                cards.forEach(card => {
                    let clientName = card.querySelector('.fw-bold').innerText.toLowerCase();

                    if (clientName.includes(searchTerm)) {
                        card.style.display = "block";
                    } else {
                        card.style.display = "none";
                    }
                });
            });
        }
        
        // 4. Carrega valores nos cards de forma dinâmica
        atualizarTotaisDinamicamente(); 
    
});

// LÓGICA DE SALVAR NOTA COM ENTER
document.addEventListener('keypress', function (e) {
    if (e.target.id === 'noteInput' && e.key === 'Enter') {
        const input = e.target;
        const mensagem = input.value.trim();
        const clienteId = input.getAttribute('data-id-cliente');

        if (mensagem !== '') {
            input.disabled = true;

        fetch(`${window.location.origin}/admin/clientes/addNota`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `cliente_id=${clienteId}&mensagem=${encodeURIComponent(mensagem)}`
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                const agora = new Date().toLocaleString('pt-BR');
                const timeline = document.getElementById('timeline-historico');

                const novaNotaHtml = `
                    <div class="timeline-item border-start ps-3 pb-3 position-relative" style="margin-left: 10px; animation: highlight 2s ease-out;">
                        <div style="position: absolute; left: -6px; top: 5px; width: 10px; height: 10px; background: #4f46e5; border-radius: 50%;"></div>
                        <small class="text-muted fw-bold d-block">${agora} - Você</small>
                        <div class="text-dark small">${mensagem}</div>
                    </div>`;

                if (timeline.querySelector('.alert-info') || timeline.innerText.includes('Nenhum registro')) {
                    timeline.innerHTML = '';
                }

                timeline.insertAdjacentHTML('afterbegin', novaNotaHtml);

                input.value = '';
                input.disabled = false;
                input.focus();

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Falha ao salvar nota',
                    text: res.message || 'Ocorreu um erro inesperado.'
                });
            }
            input.disabled = false;
            input.focus();
        })
        .catch(err => {
            console.error('Erro no Fetch:', err);
            input.disabled = false;
        });
        }
    }
});

// LÓGICA DE ABRIR CADA CARD
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-historico'); 
    
    if (btn) {
        const clientId = btn.getAttribute('data-id');
        const clientNome = btn.getAttribute('data-nome');
        
        const cardPai = btn.closest('.card');
        const valorProposta = cardPai.querySelector('.badge.text-success').innerText;
        const telefone = cardPai.querySelector('.small.text-muted').innerText;
        const email = btn.getAttribute('data-email') || ''; 

        const timeline = document.getElementById('timeline-historico');
        const loader = document.getElementById('historico-carregando');
        const inputNota = document.getElementById('noteInput');

        document.getElementById('modal-nome-cliente').innerText = clientNome;
        document.getElementById('modal-valor-proposta').innerText = valorProposta;
        inputNota.setAttribute('data-id-cliente', clientId);
        inputNota.value = ''; 

        const foneLimpo = telefone.replace(/\D/g, '');
        document.getElementById('link-call').href = `tel:${foneLimpo}`;
        document.getElementById('link-whatsapp').href = `https://wa.me/55${foneLimpo}`;
        document.getElementById('link-email').href = `mailto:${email}?subject=Contato CRM`;

        timeline.innerHTML = '';
        loader.classList.remove('d-none');
        
        const modalElement = document.getElementById('modalHistorico');
        const myModal = bootstrap.Modal.getOrCreateInstance(modalElement);
        myModal.show();

        fetch(`${window.location.origin}/admin/clientes/historico/${clientId}`)
            .then(response => response.json())
            .then(data => {
                loader.classList.add('d-none');
                if (data.logs) {
                    renderTimeline(data.logs); 
                } else {
                    renderTimeline([]); 
                }               
                
                const formTarefa = document.getElementById('form-tarefa');
                const displayTarefa = document.getElementById('display-tarefa');
                const inputId = document.getElementById('modal-cliente-id');

                if (inputId) inputId.value = clientId;

                if (data.next_step_desc && data.next_step_desc.trim() !== "") {
                    formTarefa.classList.add('d-none');
                    displayTarefa.classList.remove('d-none');

                    document.getElementById('lbl-tarefa-desc').innerText = data.next_step_desc;
                    let dataFormatada = data.next_step_at ? new Date(data.next_step_at + 'T12:00:00').toLocaleDateString('pt-BR') : 'Sem data';
                    document.getElementById('lbl-tarefa-data').innerText = 'Retorno em: ' + dataFormatada;
                } else {
                    formTarefa.classList.remove('d-none');
                    displayTarefa.classList.add('d-none');

                    document.getElementById('input-next-desc').value = '';
                    document.getElementById('input-next-date').value = '';
                }
            })
            .catch(error => {
                loader.classList.add('d-none');
                timeline.innerHTML = '<div class="alert alert-danger">Erro ao carregar histórico.</div>';
                console.error('Erro:', error);
            });
        
        const inputData = document.getElementById('input-next-date'); 
        if (inputData) {
            const hoje = new Date().toISOString().split('T')[0];
            inputData.setAttribute('min', hoje);
        }
    }
});

// EVENTO: SALVAR AGENDAMENTO
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'btn-save-next') {
        const id = document.getElementById('modal-cliente-id').value;
        const desc = document.getElementById('input-next-desc').value;
        const date = document.getElementById('input-next-date').value;

        if (!desc){
            return Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Descreva o que precisa ser feito no agendamento.'
            });
        } 

        fetch(`${window.location.origin}/admin/clientes/setNextStep`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&desc=${encodeURIComponent(desc)}&date=${date}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Toast.fire({ icon: 'success', title: 'Agendado!' });
                
                document.getElementById('form-tarefa').classList.add('d-none');
                document.getElementById('display-tarefa').classList.remove('d-none');
                document.getElementById('lbl-tarefa-desc').innerText = desc;
                
                let dataFormatada = date ? new Date(date + 'T12:00:00').toLocaleDateString('pt-BR') : 'Sem data';
                document.getElementById('lbl-tarefa-data').innerText = 'Prazo: ' + dataFormatada;
                
                const cardElement = document.getElementById(`client-${id}`);
                if (cardElement) {
                    const hoje = new Date().toLocaleDateString('en-CA'); 

                    let statusTarefaText = '';
                    let badgeClass = '';
                    let dataTarefaAttr = 'agendado';

                    if (!date) {
                        dataTarefaAttr = 'sem-tarefa';
                    } else if (date < hoje) {
                        statusTarefaText = 'Atrasado';
                        badgeClass = 'bg-danger';
                        dataTarefaAttr = 'atrasado';
                    } else if (date === hoje) {
                        statusTarefaText = 'Hoje';
                        badgeClass = 'bg-warning text-dark';
                        dataTarefaAttr = 'hoje';
                    } else {
                        const partesData = date.split('-'); 
                        statusTarefaText = `${partesData[2]}/${partesData[1]}`;
                        badgeClass = 'bg-info';
                    }

                    cardElement.setAttribute('data-tarefa', dataTarefaAttr);

                    const badgeAntigo = cardElement.querySelector('.kanban-orelha-fixa');
                    if (badgeAntigo) {
                        badgeAntigo.remove();
                    }

                    if (date) {
                        const novoBadgeHtml = `
                            <span class="badge ${badgeClass} shadow-sm kanban-orelha-fixa">
                                <i class="fas fa-calendar-check me-1"></i> ${statusTarefaText}
                            </span>`;
                        cardElement.insertAdjacentHTML('afterbegin', novoBadgeHtml);
                    }
                }
            }
        });
    }
});

// EVENTO: CONCLUIR TAREFA (CHECKBOX)
document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'check-concluir') {
        if (e.target.checked) {
            const id = document.getElementById('modal-cliente-id').value;
            
            fetch(`${window.location.origin}/admin/clientes/completeNextStep`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Toast.fire({ icon: 'success', title: 'Tarefa concluída!' });
                    document.getElementById('display-tarefa').classList.add('d-none');
                    document.getElementById('form-tarefa').classList.remove('d-none');
                    document.getElementById('input-next-desc').value = '';
                    document.getElementById('input-next-date').value = '';
                    e.target.checked = false;

                    atualizarHistoricoLog(id);
                    
                    const cardId = document.getElementById('modal-cliente-id').value;
                    const cardElement = document.getElementById(`client-${cardId}`);
                    const badge = cardElement.querySelector('.badge.bg-danger, .badge.bg-warning, .badge.bg-info');

                    if (badge) {
                        badge.remove(); 
                    }
                }
            });
        }
    }
});

// FUNÇÃO PARA RENDERIZAR A TIMELINE
function renderTimeline(data) {
    const timeline = document.getElementById('timeline-historico');
    if (data.length === 0) {
        timeline.innerHTML = '<div class="text-center text-muted small py-3">Nenhum registro encontrado.</div>';
        return;
    }

    let html = '';
    data.forEach(log => {
        const dataFormatada = new Date(log.created_at || log.data_criacao).toLocaleString('pt-BR');
        html += `
            <div class="timeline-item border-start ps-3 pb-3 position-relative" style="margin-left: 10px;">
                <div style="position: absolute; left: -6px; top: 5px; width: 10px; height: 10px; background: #cbd5e1; border-radius: 50%;"></div>
                <small class="text-muted fw-bold d-block">${dataFormatada} - ${log.usuario_nome ?? 'Sistema'}</small>
                <div class="text-dark small">${log.acao || log.mensagem}</div>
            </div>`;
    });
    timeline.innerHTML = html;
}

// --- FUNÇÃO DE RECALCULO DE MÉTRICAS EM TEMPO REAL ---
function atualizarTotaisDinamicamente() {
    let totalLead = 0;
    let totalProposta = 0;
    let totalNegociacao = 0;
    let totalFechado = 0;
    
    let totalLeadsAtivosContador = 0;
    let totalFechadosContador = 0; 

    document.querySelectorAll('.kanban-column').forEach(coluna => {
        const colunaId = coluna.id; 
        const cards = coluna.querySelectorAll('.draggable');

        let somaColuna = 0;
        cards.forEach(card => {
            let valor = parseFloat(card.getAttribute('data-valor')) || 0;
            somaColuna += valor;

            if (colunaId === 'fechado') {
                totalFechadosContador++;
            } else {
                totalLeadsAtivosContador++;
            }
        });

        if (colunaId === 'lead') totalLead = somaColuna;
        if (colunaId === 'proposta') totalProposta = somaColuna;
        if (colunaId === 'negociacao') totalNegociacao = somaColuna;
        if (colunaId === 'fechado') totalFechado = somaColuna;

        const badgeColuna = document.getElementById(`total-${colunaId}`);
        if (badgeColuna) {
            badgeColuna.innerText = somaColuna.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }
    });

    let taxaConversao = 0;
    const totalGeralDeLeads = totalLeadsAtivosContador + totalFechadosContador;
    
    if (totalGeralDeLeads > 0) {
        taxaConversao = (totalFechadosContador / totalGeralDeLeads) * 100;
    }

    const pipelineTotal = totalLead + totalProposta + totalNegociacao;

    const elFaturamento = document.getElementById('dash-faturamento');
    const elPipeline = document.getElementById('dash-pipeline');
    const elAtivosQtd = document.getElementById('dash-ativos-qtd');
    const elConversao = document.getElementById('dash-conversao'); 

    if (elFaturamento) {
        elFaturamento.innerText = totalFechado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }
    if (elPipeline) {
        elPipeline.innerText = pipelineTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }
    if (elAtivosQtd) {
        elAtivosQtd.innerText = totalLeadsAtivosContador;
    }
    if (elConversao) {
        elConversao.innerText = taxaConversao.toFixed(1).replace('.', ',') + '%';
    }
}

function atualizarHistoricoLog(clientId) {
    fetch(`${window.location.origin}/admin/clientes/historico/${clientId}`)
        .then(response => response.json())
        .then(data => {
            renderTimeline(data.logs || data);
        });
}

function confirmarGanho(id, itemElement) {
    Swal.fire({
        title: 'Parabéns pela venda!',
        text: "Deseja finalizar este lead como GANHO?",
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Sim, faturou!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            enviarFinalizacao(id, 'ganho', '', itemElement);
        } else {
            location.reload();
        }
    });
}

function confirmarPerda(id, itemElement) {
    Swal.fire({
        title: 'Qual o motivo da perda?',
        input: 'select',
        inputOptions: {
            'Preço': 'Preço alto',
            'Concorrência': 'Fechou com concorrente',
            'Desistência': 'Desistiu da compra',
            'Sem contato': 'Não atende mais'
        },
        showCancelButton: true,
        confirmButtonText: 'Confirmar Perda',
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            enviarFinalizacao(id, 'perdido', result.value, itemElement);
        } else {
            location.reload();
        }
    });
}

// --- 🔥 ALTERAÇÃO CENTRAL AQUI 🔥 ---
function enviarFinalizacao(id, statusFinal, motivo = '', itemElement = null) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('status_final', statusFinal);
    formData.append('motivo', motivo);

    fetch(`${window.location.origin}/admin/clientes/finalizar`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content')
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (itemElement) {
                itemElement.remove();
            } else {
                const el = document.getElementById('client-' + id);
                if (el) el.remove();
            }
            
            // O pulo do gato: Recarregar a página garante que os contadores php do topo 
            // e os cálculos do banco venham perfeitamente atualizados, sem "surgir" um card 
            // fantasma na coluna de fechados (já que ele foi arquivado pelo botão).
            location.reload();
        } else {
            Swal.fire('Erro', data.message || 'Erro ao finalizar.', 'error').then(() => location.reload());
        }
    })
    .catch(err => {
        console.error(err);
        location.reload();
    });
}