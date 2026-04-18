/**
 * Lógica do Kanban - CRM
 */
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('kanban-container');
    const endpoint  = container.getAttribute('data-url'); // Pega a URL da View
    const colunas   = document.querySelectorAll('.kanban-column');

    colunas.forEach(coluna => {
        new Sortable(coluna, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'bg-light-blue',
            onEnd: function (evt) {
                // 1. Pega o ID do elemento HTML que foi movido (ex: "client-5")
                const itemId = evt.item.id; 

                // 2. Remove o prefixo para pegar só o número (o ID do banco)
                const clientId = itemId.replace('client-', ''); 

                // 3. Pega o ID da coluna onde o card caiu (o novo status)
                const newStatus = evt.to.id; 

                const url = document.getElementById('kanban-container').getAttribute('data-url');

                // Agora o fetch vai funcionar porque 'clientId' existe!
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: clientId, // <--- Aqui o JS não vai mais reclamar
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'success') {
                        alert('Erro ao salvar no banco!');
                        location.reload(); // Recarrega para voltar o card pro lugar original
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro de conexão!');
                });
            },
        });
    });
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