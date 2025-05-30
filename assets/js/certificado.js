document.addEventListener('DOMContentLoaded', function() {
    var form = document.querySelector('form');
    var fileInput = document.querySelector('input[type="file"]');
    var passwordInput = document.querySelector('input[type="password"]');
    var submitButton = document.querySelector('button[type="submit"]');

    if (form) {
        form.addEventListener('submit', function(e) {
            if (!fileInput.value || !passwordInput.value) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
                return false;
            }
            submitButton.disabled = true;
            submitButton.textContent = 'Processando...';
            return true;
        });
    }
}); 