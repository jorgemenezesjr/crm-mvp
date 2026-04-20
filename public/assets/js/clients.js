document.addEventListener('DOMContentLoaded', function() {
    const inputValor = document.getElementById('valor');

    if (inputValor) {
        inputValor.addEventListener('input', function(e) {
            let value = e.target.value;

            // Remove tudo que não é dígito
            value = value.replace(/\D/g, "");

            // Formata como moeda (ex: 100050 -> 1.000,50)
            value = (value / 100).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            e.target.value = value;
        });
    }
});