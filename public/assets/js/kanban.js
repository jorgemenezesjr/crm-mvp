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
                renderTimeline(data);
            })
            .catch(error => {
                loader.classList.add('d-none');
                timeline.innerHTML = '<div class="alert alert-danger">Erro ao carregar histórico.</div>';
                console.error('Erro:', error);
            });
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

                // Cria o HTML da nova nota
                const novaNotaHtml = `
                    <div class="timeline-item border-start ps-3 pb-3 position-relative" style="margin-left: 10px; animation: highlight 2s ease-out;">
                        <div style="position: absolute; left: -6px; top: 5px; width: 10px; height: 10px; background: #4f46e5; border-radius: 50%;"></div>
                        <small class="text-muted fw-bold d-block">${agora} - Você</small>
                        <div class="text-dark small">${mensagem}</div>
                    </div>`;

                const timeline = document.getElementById('timeline-historico');
                timeline.insertAdjacentHTML('afterbegin', novaNotaHtml);
                // Faz o scroll voltar para o topo para mostrar que a nota entrou
                timeline.scrollTop = 0;
                

                // Remove mensagem de "Nenhum registro" se ela existir
                if (timeline.innerHTML.includes('alert-info') || timeline.innerHTML.includes('Nenhum registro')) {
                    timeline.innerHTML = '';
                }


                input.value = ''; // Limpa o campo
            } else {
                alert('Erro: ' + (res.message || 'Falha ao salvar'));
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

