document.addEventListener('click', function (e) {
    // Verifica se clicou no nome do cliente (classe btn-historico)
    if (e.target.classList.contains('btn-historico')) {
        const clientId = e.target.getAttribute('data-id');
        const clientNome = e.target.getAttribute('data-nome');
        const timeline = document.getElementById('timeline-historico');
        const loader = document.getElementById('historico-carregando');

        // Configura o Modal
        document.getElementById('modalHistoricoLabel').innerText = `Histórico: ${clientNome}`;
        timeline.innerHTML = '';
        loader.classList.remove('d-none');
        
        // Abre o modal (usando o objeto global do Bootstrap)
        const myModal = new bootstrap.Modal(document.getElementById('modalHistorico'));
        myModal.show();

        // Busca os logs via Fetch
        fetch(`${window.location.origin}/admin/clientes/historico/${clientId}`)
            .then(response => response.json())
            .then(data => {
                loader.classList.add('d-none');
                if (data.length === 0) {
                    timeline.innerHTML = '<div class="alert alert-info">Nenhum registro encontrado para este cliente.</div>';
                    return;
                }

                // Monta a lista de logs
                let html = '<ul class="list-group list-group-flush">';
                data.forEach(log => {
                    // Formata a data para o padrão brasileiro
                    const dataFormatada = new Date(log.created_at).toLocaleString('pt-BR');

                    html += `
                        <li class="list-group-item px-0">
                            <div class="d-flex justify-content-between">
                                <strong class="small text-primary">${log.usuario_nome ?? 'Sistema'}</strong>
                                <small class="text-muted">${dataFormatada}</small>
                            </div>
                            <div class="mt-1">${log.acao}</div>
                        </li>`;
                });
                html += '</ul>';
                timeline.innerHTML = html;
            })
            .catch(error => {
                loader.classList.add('d-none');
                timeline.innerHTML = '<div class="alert alert-danger">Erro ao carregar histórico.</div>';
                console.error('Erro:', error);
            });
    }
});