document.addEventListener('DOMContentLoaded', function() {
    // Referências aos elementos do formulário
    const form = document.getElementById('register-form');
    const nameInput = document.getElementById('name');
    const cpfInput = document.getElementById('cpf');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const departmentSelect = document.getElementById('department_id');
    
    // Formatação automática do CPF
    cpfInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove caracteres não-numéricos
        
        if (value.length > 11) {
            value = value.slice(0, 11); // Limita a 11 dígitos
        }
        
        // Aplica a formatação
        if (value.length > 9) {
            e.target.value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, "$1.$2.$3-$4");
        } else if (value.length > 6) {
            e.target.value = value.replace(/^(\d{3})(\d{3})(\d{3})$/, "$1.$2.$3");
        } else if (value.length > 3) {
            e.target.value = value.replace(/^(\d{3})(\d{3})$/, "$1.$2");
        } else {
            e.target.value = value;
        }
    });
    
    // Formatação do nome (primeira letra de cada palavra em maiúscula)
    nameInput.addEventListener('blur', function(e) {
        if (e.target.value) {
            // Converte para minúsculas e depois capitaliza a primeira letra de cada palavra
            const words = e.target.value.toLowerCase().trim().split(/\s+/);
            for (let i = 0; i < words.length; i++) {
                if (words[i]) {
                    words[i] = words[i][0].toUpperCase() + words[i].substring(1);
                }
            }
            e.target.value = words.join(' ');
        }
    });
    
    // Função para mostrar mensagens de erro
    function showError(input, message) {
        const formGroup = input.closest('.form-group');
        let errorDiv = formGroup.querySelector('.error-message');
        
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            formGroup.appendChild(errorDiv);
        }
        
        errorDiv.textContent = message;
        input.classList.add('error');
    }
    
    // Função para remover mensagens de erro
    function hideError(input) {
        const formGroup = input.closest('.form-group');
        const errorDiv = formGroup.querySelector('.error-message');
        
        if (errorDiv) {
            errorDiv.remove();
        }
        
        input.classList.remove('error');
    }
    
    // Validação de e-mail
    function isValidEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    // Validação de CPF (apenas formato, não verifica dígitos)
    function isValidCPFFormat(cpf) {
        const re = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
        return re.test(cpf);
    }
    
    // Validação de força da senha
    function isStrongPassword(password) {
        return password.length >= 6 && 
               /[A-Z]/.test(password) && 
               /[a-z]/.test(password) && 
               /[0-9]/.test(password);
    }
    
    // Validação em tempo real para os campos
    nameInput.addEventListener('input', () => {
        if (nameInput.value.trim()) {
            hideError(nameInput);
        }
    });
    
    cpfInput.addEventListener('input', () => {
        if (cpfInput.value && isValidCPFFormat(cpfInput.value)) {
            hideError(cpfInput);
        }
    });
    
    emailInput.addEventListener('input', () => {
        if (emailInput.value && isValidEmail(emailInput.value)) {
            hideError(emailInput);
        }
    });
    
    departmentSelect.addEventListener('change', () => {
        if (departmentSelect.value) {
            hideError(departmentSelect);
        }
    });
    
    passwordInput.addEventListener('input', () => {
        if (passwordInput.value) {
            if (isStrongPassword(passwordInput.value)) {
                hideError(passwordInput);
            } else if (passwordInput.value.length >= 6) {
                showError(passwordInput, 'A senha deve conter letras maiúsculas, minúsculas e números');
            }
        }
        
        // Verificar também a confirmação de senha
        if (confirmPasswordInput.value && confirmPasswordInput.value !== passwordInput.value) {
            showError(confirmPasswordInput, 'As senhas não coincidem');
        } else if (confirmPasswordInput.value) {
            hideError(confirmPasswordInput);
        }
    });
    
    confirmPasswordInput.addEventListener('input', () => {
        if (confirmPasswordInput.value === passwordInput.value) {
            hideError(confirmPasswordInput);
        } else if (confirmPasswordInput.value) {
            showError(confirmPasswordInput, 'As senhas não coincidem');
        }
    });
    
    // Validação do formulário antes de enviar
    form.addEventListener('submit', function(event) {
        let isValid = true;
        
        // Limpar mensagens de erro anteriores
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.form-group input, .form-group select').forEach(el => el.classList.remove('error'));
        
        // Validar nome
        if (!nameInput.value.trim()) {
            isValid = false;
            showError(nameInput, 'Nome completo é obrigatório');
        } else if (nameInput.value.trim().split(/\s+/).length < 2) {
            isValid = false;
            showError(nameInput, 'Informe nome e sobrenome');
        }
        
        // Validar CPF
        if (!cpfInput.value) {
            isValid = false;
            showError(cpfInput, 'CPF é obrigatório');
        } else if (!isValidCPFFormat(cpfInput.value)) {
            isValid = false;
            showError(cpfInput, 'Formato de CPF inválido');
        }
        
        // Validar e-mail
        if (!emailInput.value) {
            isValid = false;
            showError(emailInput, 'E-mail é obrigatório');
        } else if (!isValidEmail(emailInput.value)) {
            isValid = false;
            showError(emailInput, 'E-mail inválido');
        }
        
        // Validar secretaria
        if (!departmentSelect.value) {
            isValid = false;
            showError(departmentSelect, 'Selecione uma secretaria');
        }
        
        // Validar senha
        if (!passwordInput.value) {
            isValid = false;
            showError(passwordInput, 'Senha é obrigatória');
        } else if (!isStrongPassword(passwordInput.value)) {
            isValid = false;
            showError(passwordInput, 'A senha deve ter pelo menos 6 caracteres, com letras maiúsculas, minúsculas e números');
        }
        
        // Validar confirmação de senha
        if (!confirmPasswordInput.value) {
            isValid = false;
            showError(confirmPasswordInput, 'Confirme sua senha');
        } else if (confirmPasswordInput.value !== passwordInput.value) {
            isValid = false;
            showError(confirmPasswordInput, 'As senhas não coincidem');
        }
        
        if (!isValid) {
            event.preventDefault();
        }
    });
    
    // Função para alternar visibilidade da senha
    window.togglePassword = function(id) {
        const passwordField = document.getElementById(id);
        const icon = passwordField.nextElementSibling.querySelector('i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };
});