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

        // 1. Configuração das Colunas (Seu código do GitHub)
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

                    // --- AQUI ESTÁ O PULO DO GATO ---
                    // Se o destino for um botão, NÃO faz o fetch de movimentação
                    if (newStatus === 'zone-success' || newStatus === 'zone-danger') {
                        return; 
                    }

                    // Se caiu em uma coluna normal, segue seu código original:
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
                            Toast.fire({ icon: 'success', title: 'Status atualizado!' });
                            atualizarTotaisDinamicamente();
                        }
                    });
                },
            });
        });

        // 2. Lógica separada apenas para os BOTÕES
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
    
});



// LÓGICA DE SALVAR NOTA COM ENTER
document.addEventListener('keypress', function (e) {
    if (e.target.id === 'noteInput' && e.key === 'Enter') {
        const input = e.target;
        const mensagem = input.value.trim();
        const clienteId = input.getAttribute('data-id-cliente');

        if (mensagem !== '') {
            input.disabled = true;

        // No kanban.js (dentro do evento de Keypress Enter)
        fetch(`${window.location.origin}/admin/clientes/addNota`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `cliente_id=${clienteId}&mensagem=${encodeURIComponent(mensagem)}`
        })
        .then(response => response.json())
        .then(res => {
            console.log('Resposta do servidor:', res); // <--- DEBUG: Veja isso no F12

            if (res.status === 'success') {
                const agora = new Date().toLocaleString('pt-BR');
                const timeline = document.getElementById('timeline-historico');

                // 1. Criar o HTML da nova nota
                const novaNotaHtml = `
                    <div class="timeline-item border-start ps-3 pb-3 position-relative" style="margin-left: 10px; animation: highlight 2s ease-out;">
                        <div style="position: absolute; left: -6px; top: 5px; width: 10px; height: 10px; background: #4f46e5; border-radius: 50%;"></div>
                        <small class="text-muted fw-bold d-block">${agora} - Você</small>
                        <div class="text-dark small">${mensagem}</div>
                    </div>`;

                // 2. CORREÇÃO: Se houver mensagem de "Nenhum registro", limpa ANTES de inserir
                if (timeline.querySelector('.alert-info') || timeline.innerText.includes('Nenhum registro')) {
                    timeline.innerHTML = '';
                }

                // 3. Insere no topo
                timeline.insertAdjacentHTML('afterbegin', novaNotaHtml);

                // 4. Limpa e foca o campo
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


//LÓGICA DE ABRIR CADA CARD
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-historico'); // Melhor que classList.contains para evitar erros em sub-elementos
    
    if (btn) {
        const clientId = btn.getAttribute('data-id');
        const clientNome = btn.getAttribute('data-nome');
        
        // Busca dados que estão no card pai (PHP enviou para o card)
        const cardPai = btn.closest('.card');
        const valorProposta = cardPai.querySelector('.badge.text-success').innerText;
        const telefone = cardPai.querySelector('.small.text-muted').innerText;
        // Se você tiver o email no card, pegue aqui, senão deixamos um padrão
        const email = btn.getAttribute('data-email') || ''; 

        const timeline = document.getElementById('timeline-historico');
        const loader = document.getElementById('historico-carregando');
        const inputNota = document.getElementById('noteInput');

        // 1. Configura a Identidade na Modal
        document.getElementById('modal-nome-cliente').innerText = clientNome;
        document.getElementById('modal-valor-proposta').innerText = valorProposta;
        inputNota.setAttribute('data-id-cliente', clientId);
        inputNota.value = ''; // Limpa o campo de nota

        // 2. Configura os Links de Ação
        const foneLimpo = telefone.replace(/\D/g, '');
        document.getElementById('link-call').href = `tel:${foneLimpo}`;
        document.getElementById('link-whatsapp').href = `https://wa.me/55${foneLimpo}`;
        document.getElementById('link-email').href = `mailto:${email}?subject=Contato CRM`;

        // 3. Reseta Timeline e Abre Modal
        timeline.innerHTML = '';
        loader.classList.remove('d-none');
        
        const modalElement = document.getElementById('modalHistorico');
        const myModal = bootstrap.Modal.getOrCreateInstance(modalElement);
        myModal.show();

        // 4. Busca os logs via Fetch
        fetch(`${window.location.origin}/admin/clientes/historico/${clientId}`)
            .then(response => response.json())
            .then(data => {
                loader.classList.add('d-none');
                // 1. Renderiza o histórico (seus logs antigos)
                // Se o seu PHP retornar um objeto com { logs: [], next_step_desc: ... }
                // ajuste para renderTimeline(data.logs);
                
                // CORREÇÃO: Enviar apenas a chave 'logs' para a função de desenho
                    if (data.logs) {
                        renderTimeline(data.logs); 
                    } else {
                        renderTimeline([]); // Se não houver nada, envia vazio
                    }               
                
                
                // --- NOVA LÓGICA DE ALTERNÂNCIA DE ESTADOS ---
                const formTarefa = document.getElementById('form-tarefa');
                const displayTarefa = document.getElementById('display-tarefa');
                const inputId = document.getElementById('modal-cliente-id');

                // Garante que o ID do cliente está salvo na modal para o agendamento saber quem ele é
                if (inputId) inputId.value = clientId;

                // Verifica se o cliente já tem uma tarefa pendente
                if (data.next_step_desc && data.next_step_desc.trim() !== "") {
                    // ESTADO: Tarefa Ativa (Mostra o checkbox e a descrição)
                    formTarefa.classList.add('d-none');
                    displayTarefa.classList.remove('d-none');

                    document.getElementById('lbl-tarefa-desc').innerText = data.next_step_desc;

                    // Formata a data se ela existir
                    let dataFormatada = data.next_step_at ? new Date(data.next_step_at + 'T12:00:00').toLocaleDateString('pt-BR') : 'Sem data';
                    document.getElementById('lbl-tarefa-data').innerText = 'Retorno em: ' + dataFormatada;
                } else {
                    // ESTADO: Criar Novo (Mostra os campos de input vazios)
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
        
        // 5. Impede seleção de datas retroativas
        const inputData = document.getElementById('input-next-date'); // Ajuste para o ID real do seu input
        if (inputData) {
            const hoje = new Date().toISOString().split('T')[0];
            inputData.setAttribute('min', hoje);
        }
    }
});


// --- EVENTO: SALVAR AGENDAMENTO ---
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
                
                // Troca o visual no modal para "Tarefa Ativa"
                document.getElementById('form-tarefa').classList.add('d-none');
                document.getElementById('display-tarefa').classList.remove('d-none');
                document.getElementById('lbl-tarefa-desc').innerText = desc;
                
                let dataFormatada = date ? new Date(date + 'T12:00:00').toLocaleDateString('pt-BR') : 'Sem data';
                document.getElementById('lbl-tarefa-data').innerText = 'Prazo: ' + dataFormatada;
                
                // --- ATUALIZAÇÃO DO CARD NO KANBAN ---
                const cardElement = document.getElementById(`client-${id}`);
                if (cardElement) {
                    // Pega o fuso horário local correto para comparar as datas sem erros de fuso
                    const hoje = new Date().toLocaleDateString('en-CA'); // Retorna YYYY-MM-DD local

                    let statusTarefaText = '';
                    let badgeClass = '';
                    let dataTarefaAttr = 'agendado';

                    // 1. Define os textos e classes baseados na data selecionada
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
                        // Se for futuro, pega apenas o "Dia/Mês" (ex: 15/06)
                        const partesData = date.split('-'); // [Ano, Mês, Dia]
                        statusTarefaText = `${partesData[2]}/${partesData[1]}`;
                        badgeClass = 'bg-info';
                    }

                    // 2. Atualiza o atributo para o CSS mudar a cor da borda imediatamente
                    cardElement.setAttribute('data-tarefa', dataTarefaAttr);

                    // 3. Remove o badge antigo (se houver um) para não duplicar na tela
                    const badgeAntigo = cardElement.querySelector('.kanban-orelha-fixa');
                    if (badgeAntigo) {
                        badgeAntigo.remove();
                    }

                    // 4. Se houver uma data definida, cria e injeta o novo Badge no topo do card
                    if (date) {
                        const novoBadgeHtml = `
                            <span class="badge ${badgeClass} shadow-sm kanban-orelha-fixa">
                                <i class="fas fa-calendar-check me-1"></i> ${statusTarefaText}
                            </span>
                        `;
                        // Insere logo no início da div do card para respeitar a sua estrutura
                        cardElement.insertAdjacentHTML('afterbegin', novoBadgeHtml);
                    }
                }
            }
        });
    }
});

// --- EVENTO: CONCLUIR TAREFA (CHECKBOX) ---
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
                    // 1. Reset visual do widget
                    document.getElementById('display-tarefa').classList.add('d-none');
                    document.getElementById('form-tarefa').classList.remove('d-none');
                    document.getElementById('input-next-desc').value = '';
                    document.getElementById('input-next-date').value = '';
                    e.target.checked = false;

                    // 2. ATUALIZAÇÃO EM TEMPO REAL:
                    // Chamamos a mesma função que você usa para abrir a modal 
                    // ou apenas o fetch do histórico. Exemplo:
                    atualizarHistoricoLog(id);
                    
                    // 3. Dentro do .then(res => { if(res.status === 'success') ...
                    const cardId = document.getElementById('modal-cliente-id').value;
                    const cardElement = document.getElementById(`client-${cardId}`);
                    const badge = cardElement.querySelector('.badge.bg-danger, .badge.bg-warning, .badge.bg-info');

                    if (badge) {
                        badge.remove(); // Remove o ícone de agendamento do card instantaneamente
                    }
                }
            });
        }
    }
});



function saveStatus(id, status, url) {
    const params = new URLSearchParams();
    params.append('id', id);
    params.append('status', status);

    fetch(url, {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content')
    },
        body: JSON.stringify({
        id: clientId,
        status: newStatus
    })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Erro ao mover cliente.');
            location.reload(); // Recarrega para voltar o card se deu erro
        }
    })
    .catch(error => console.error('Erro:', error));
}



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




function atualizarTotaisDinamicamente() {
    const colunas = ['lead', 'proposta', 'negociacao', 'fechado'];

    colunas.forEach(idColuna => {
        const coluna = document.getElementById(idColuna);
        const cards = coluna.querySelectorAll('.draggable');
        let soma = 0;

        cards.forEach(card => {
            // Pega o valor puro que colocamos no data-valor
            soma += parseFloat(card.getAttribute('data-valor')) || 0;
        });

        // Seleciona o badge pelo ID que criamos
        const badge = document.getElementById(`total-${idColuna}`);
        if (badge) {
            badge.innerText = soma.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }
    });
}



// Função auxiliar para não repetir código
function atualizarHistoricoLog(clientId) {
    const timeline = document.getElementById('timeline-historico');
    
    fetch(`${window.location.origin}/admin/clientes/historico/${clientId}`)
        .then(response => response.json())
        .then(data => {
            // Usa a sua função que já existe para desenhar a timeline
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
            // Remove o card apenas após o sucesso no banco
            if (itemElement) {
                itemElement.remove();
            } else {
                const el = document.getElementById('client-' + id);
                if (el) el.remove();
            }
            
            atualizarTotaisDinamicamente();
            Toast.fire({ 
                icon: statusFinal === 'ganho' ? 'success' : 'info', 
                title: statusFinal === 'ganho' ? 'Venda realizada!' : 'Lead arquivado' 
            });
        } else {
            Swal.fire('Erro', data.message || 'Erro ao finalizar.', 'error').then(() => location.reload());
        }
    })
    .catch(err => {
        console.error(err);
        location.reload();
    });
}