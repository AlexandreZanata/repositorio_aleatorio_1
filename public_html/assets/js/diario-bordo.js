document.addEventListener('DOMContentLoaded', function() {
    // --- Lógica da Página 1: Busca de Veículo com Sugestões ---
    const prefixInput = document.getElementById('prefixo');
    const suggestionsContainer = document.getElementById('prefix-suggestions');
    const vehicleInfo = document.getElementById('vehicle-info');
    const submitBtn = document.getElementById('submit-btn');
    const vehicleForm = document.getElementById('vehicle-form');

    if (prefixInput && vehicleForm) {
        
        // Impede o envio do formulário com a tecla Enter no campo de busca
        prefixInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        prefixInput.addEventListener('keyup', function() {
            const term = this.value;
            // Começa a buscar com 2 ou mais caracteres para evitar sobrecarga
            if (term.length < 2) {
                suggestionsContainer.innerHTML = '';
                suggestionsContainer.style.display = 'none';
                return;
            }

            // Busca os dados no back-end (ajuste o caminho se necessário)
            fetch(`../api/process_diario.php?action=search_vehicle&prefix=${term}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsContainer.innerHTML = ''; // Limpa sugestões antigas
                    if (data.length > 0) {
                        const ul = document.createElement('ul');
                        data.forEach(vehicle => {
                            const li = document.createElement('li');
                            // Mostra o prefixo em negrito e a placa
                            li.innerHTML = `<strong>${vehicle.prefix}</strong> - ${vehicle.license_plate}`;
                            
                            // Guarda todos os dados do veículo no elemento para uso posterior
                            li.dataset.vehicleId = vehicle.id;
                            li.dataset.prefix = vehicle.prefix;
                            li.dataset.licensePlate = vehicle.license_plate;
                            li.dataset.vehicleName = vehicle.name;
                            li.dataset.departmentName = vehicle.department_name;
                            
                            ul.appendChild(li);
                        });
                        suggestionsContainer.appendChild(ul);
                        suggestionsContainer.style.display = 'block';
                    } else {
                        suggestionsContainer.style.display = 'none';
                    }
                })
                .catch(error => console.error('Erro ao buscar veículos:', error));
        });

        // Evento de clique para selecionar um veículo da lista de sugestões
        suggestionsContainer.addEventListener('click', function(e) {
            const li = e.target.closest('li');
            if (li) {
                // Preenche o campo de busca com o prefixo do veículo selecionado
                prefixInput.value = li.dataset.prefix;
                
                // Preenche as informações detalhadas do veículo
                document.getElementById('placa').value = li.dataset.licensePlate;
                document.getElementById('nome-veiculo').value = li.dataset.vehicleName;
                document.getElementById('secretaria').value = li.dataset.departmentName;
                document.getElementById('vehicle_id').value = li.dataset.vehicleId;
                
                vehicleInfo.style.display = 'block'; // Mostra a caixa de informações
                submitBtn.disabled = false; // Habilita o botão de avançar
                
                suggestionsContainer.style.display = 'none'; // Esconde a lista de sugestões
            }
        });
        
        // Esconde a lista de sugestões se o usuário clicar em qualquer outro lugar da página
        document.addEventListener('click', function(e) {
            if (!prefixInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                 suggestionsContainer.style.display = 'none';
            }
        });
    }

    // --- Lógica da Página 2: Checklist ---
    const checklistForm = document.getElementById('checklist-form');
    if (checklistForm) {
        checklistForm.addEventListener('change', function(e) {
            if (e.target.type === 'radio') {
                const parent = e.target.closest('.checklist-options');
                parent.querySelectorAll('.radio-option').forEach(label => label.classList.remove('checked'));
                e.target.parentElement.classList.add('checked');

                const itemContainer = e.target.closest('.checklist-item');
                const notesContainer = itemContainer.querySelector('.notes-container');
                const notesTextarea = notesContainer.querySelector('textarea');
                
                if (e.target.value === 'problem') {
                    notesContainer.style.display = 'block';
                    notesTextarea.required = true;
                } else {
                    notesContainer.style.display = 'none';
                    notesTextarea.required = false;
                }
            }
        });
    }
    
    // --- Lógica da Página 4: Abastecimento (Abas) ---
    const tabs = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    if (tabs.length > 0) {
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const target = document.getElementById(tab.dataset.tab);
                tabContents.forEach(content => content.classList.remove('active'));
                target.classList.add('active');
            });
        });
    }
});