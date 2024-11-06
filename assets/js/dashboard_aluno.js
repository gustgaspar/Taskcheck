document.addEventListener("DOMContentLoaded", () => {
    const elements = {
        toggleSidebarButton: document.getElementById("toggleSidebar"),
        sidebar: document.getElementById("sidebar"),
        content: document.getElementById("content"),
        addActivityButton: document.getElementById("add-activity"),
        viewSentActivitiesButton: document.getElementById("view-sent-activities"),
        activityForm: document.getElementById('activity-form'),
        sentActivitiesCard: document.getElementById('sent-activities-card'),
        tabelaBody: document.querySelector(".table tbody"),
        filtrarAtividadesDropdown: document.getElementById("filtrarAtividades"),
        addActivityForm: document.querySelector('#activity-form form'),
        editActivityForm: document.getElementById("edit-activity-form"),
        messageContainer: document.getElementById('response-message'),
        viewProgressButton: document.getElementById("view-progress"),
        progressCategoriesCard: document.getElementById("progress-categories"),
        dateInput: document.getElementById("data"),
        categoriaInput: document.getElementById("categoria"),
        nomeInput: document.getElementById("nome"),
        reflexaoInput: document.getElementById("reflexao"),
        certificadoInput: document.getElementById("certificado"),
        logoutButton: document.getElementById("logout-btn")
    };

    // INICIALIZA EVENTOS AO CARREGAR A PÁGINA
    init();

    // CONFIG DOS EVENTOS
    function init() {
        // CONFIGURAÇÃO DOS BOTÕES PRINCIPAIS
        elements.toggleSidebarButton.addEventListener("click", toggleSidebar);
        elements.addActivityButton.addEventListener("click", mostrarFormularioAtividade);
        elements.viewSentActivitiesButton.addEventListener("click", mostrarCardAtividadesEnviadas);
        elements.viewProgressButton.addEventListener("click", mostrarProgressoCategorias);

        // FILTRAR RELATORIOS POR CATEGORIA PELO DROPDOWN
        elements.filtrarAtividadesDropdown.addEventListener("change", filtrarRelatoriosPorCategoria);

        // SUBMISSÃO DO FORMULÁRIO - ADICIONAR ATIVIDADE E EDICAO ATIVIDADE
        elements.addActivityForm.addEventListener("submit", validarFormulario);
        elements.editActivityForm.addEventListener("submit", salvarEdicaoAtividade);

        // LOGOUT
        elements.logoutButton.addEventListener("click", logout);

        // CARREGAR ATIVIDADES ENVIADAS AO INICIAR
        carregarAtividadesEnviadas();
    }

    // MOSTRAR/ESCONDER SIDEBAR
    function toggleSidebar() {
        elements.sidebar.classList.toggle("hidden");
        elements.content.classList.toggle("sidebar-hidden");
    }

    // MOSTRAR FORMULÁRIO DE ADICIONAR ATIVIDADE
    function mostrarFormularioAtividade(event) {
        event.preventDefault();
        elements.activityForm.style.display = 'block';
        elements.progressCategoriesCard.style.display = 'none';
        elements.sentActivitiesCard.style.display = 'none';
        elements.dateInput.addEventListener("input", tratarDataRealizacao);
        carregarCategorias();
    }

    // MOSTRAR CARD DE ATIVIDADES ENVIADAS
    function mostrarCardAtividadesEnviadas(event) {
        event.preventDefault();
        elements.activityForm.style.display = 'none';
        elements.progressCategoriesCard.style.display = 'none';
        elements.sentActivitiesCard.style.display = 'block';
        carregarAtividadesEnviadas();
        carregarCategorias();
    }

    // MOSTRAR CARDS DO PROGRESSO DE CADA CATEGORIA
    async function mostrarProgressoCategorias(event) {
        event.preventDefault();
        elements.activityForm.style.display = 'none';
        elements.sentActivitiesCard.style.display = 'none';
        elements.progressCategoriesCard.style.display = 'block';
        carregarProgressoCategorias();
    }

    // VALIDAR CAMPOS FORMULARIO ADD ATV - BOOTSTRAP
    function validarFormulario(event) {
        event.preventDefault();
        let isValid = true;

        // CAMPO DATA
        if (!elements.dateInput.value.trim() || elements.dateInput.value.trim().length < 10) {
            elements.dateInput.classList.add("is-invalid");
            isValid = false;
        } else {
            elements.dateInput.classList.remove("is-invalid");
            elements.dateInput.classList.add("is-valid");
        }

        // CAMPO CATEGORIA
        if (elements.categoriaInput.value === "Selecionar categoria") {
            elements.categoriaInput.classList.add("is-invalid");
            isValid = false;
        } else {
            elements.categoriaInput.classList.remove("is-invalid");
            elements.categoriaInput.classList.add("is-valid");
        }

        // CAMPO NOME
        if (!elements.nomeInput.value.trim()) {
            elements.nomeInput.classList.add("is-invalid");
            isValid = false;
        } else {
            elements.nomeInput.classList.remove("is-invalid");
            elements.nomeInput.classList.add("is-valid");
        }

        // CAMPO REFLEXÃO
        if (!elements.reflexaoInput.value.trim()) {
            elements.reflexaoInput.classList.add("is-invalid");
            isValid = false;
        } else {
            elements.reflexaoInput.classList.remove("is-invalid");
            elements.reflexaoInput.classList.add("is-valid");
        }

        // CAMPO CERTIFICADO
        if (!elements.certificadoInput.files.length || elements.certificadoInput.files[0].type !== "application/pdf") {
            elements.certificadoInput.classList.add("is-invalid");
            isValid = false;
        } else {
            elements.certificadoInput.classList.remove("is-invalid");
            elements.certificadoInput.classList.add("is-valid");
        }

        if (isValid) {
            adicionarAtividade();
        }
    }

    // VALIDACAO EXTRA CAMPO DATA
    function tratarDataRealizacao(event) {
        let input = event.target.value;

        // REMOVER CARACTERE NAO NUMERICO
        input = input.replace(/[^0-9]/g, '');

        // FORMATAR DD/MM/AAAA - INSERIR "/" AUTOMATICO
        if (input.length > 2 && input.length <= 4) {
            input = input.slice(0, 2) + '/' + input.slice(2);
        } else if (input.length > 4) {
            input = input.slice(0, 2) + '/' + input.slice(2, 4) + '/' + input.slice(4, 8);
        }

        event.target.value = input;

        // VERIFICAR SE A DATA É MAIOR QUE A DATA ATUAL
        if (input.length === 10) {
            const [day, month, year] = input.split('/').map(Number);
            const inputDate = new Date(year, month - 1, day);
            const currentDate = new Date();

            if (inputDate > currentDate || month > 12) {
                elements.dateInput.classList.add("is-invalid");
                elements.dateInput.classList.remove("is-valid");
            } else {
                elements.dateInput.classList.remove("is-invalid");
                elements.dateInput.classList.add("is-valid");
            }
        } else {
            elements.dateInput.classList.remove("is-invalid", "is-valid");
        }
    }

    // FILTRAR RELATORIOS POR CATEGORIA
    async function filtrarRelatoriosPorCategoria() {
        const categoriaId = elements.filtrarAtividadesDropdown.value;

        try {
            const response = await fetch(`../api/reportApi.php?action=list_by_aluno&categoria=${categoriaId}`);
            const relatoriosFiltrados = await response.json();
            exibirAtividadesEnviadas(relatoriosFiltrados);
        } catch (error) {
            console.error("Erro ao filtrar relatórios:", error);
        }
    }

    // CARREGAR CATEGORIAS PARA O DROPDOWN
    async function carregarCategorias() {
        try {
            const response = await fetch("../api/categoryApi.php?action=list");
            const categorias = await response.json();

            const categoriaDropdown = document.getElementById("categoria");
            const categoriaEditDropdown = document.getElementById("edit-categoria");

            categoriaDropdown.innerHTML = '<option selected>Selecionar categoria</option>';
            categoriaEditDropdown.innerHTML = '<option selected>Selecionar categoria</option>';

            elements.filtrarAtividadesDropdown.innerHTML = `<option value="" selected>Filtrar por categoria</option>`;

            categorias.forEach(categoria => {
                const option = document.createElement("option");
                option.value = categoria.id;
                option.text = categoria.nome;
                categoriaDropdown.add(option.cloneNode(true));
                categoriaEditDropdown.add(option);
                elements.filtrarAtividadesDropdown.add(option.cloneNode(true));
            });
        } catch (error) {
            console.error("Erro ao carregar categorias:", error);
        }
    }

    // ADICIONAR ATIVIDADE - COLETAR E ENVIAR DADOS DO FORMULÁRIO
    async function adicionarAtividade(event) {
        if (event) {
            event.preventDefault();
        }

        const nome = document.getElementById('nome').value;
        const orgao = document.getElementById('orgao').value;
        const data_realizacao = document.getElementById('data').value;
        const id_categoria = document.getElementById('categoria').value;
        const texto_reflexao = document.getElementById('reflexao').value;
        const certificado = document.getElementById('certificado').files[0];
        const messageContainer = document.getElementById('response-message');

        if (id_categoria === "Selecionar categoria") {
            messageContainer.innerHTML = `<p class="text-danger">Por favor, selecione uma categoria válida.</p>`;
            return;
        }

        if (certificado && certificado.type !== "application/pdf") {
            messageContainer.innerHTML = `<p class="text-danger">Apenas arquivos PDF são permitidos.</p>`;
            return;
        }

        const formData = new FormData();
        formData.append('nome', nome);
        formData.append('orgao', orgao);
        formData.append('data_realizacao', data_realizacao);
        formData.append('id_categoria', id_categoria);
        formData.append('texto_reflexao', texto_reflexao);
        formData.append('certificado', certificado);

        try {
            const response = await fetch('../api/reportApi.php?action=add', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                messageContainer.innerHTML = `<p class="text-success">${result.message}</p>`;
                document.querySelector('#activity-form form').reset();
                elements.dateInput.classList.remove("is-valid");
                elements.categoriaInput.classList.remove("is-valid");
                elements.nomeInput.classList.remove("is-valid");
                elements.reflexaoInput.classList.remove("is-valid");
                elements.certificadoInput.classList.remove("is-valid");
            } else {
                messageContainer.innerHTML = `<p class="text-danger">${result.message}</p>`;
            }
        } catch (error) {
            console.error('Erro ao enviar a atividade:', error);
        }
    }

    // CARREGAR ATIVIDADES ENVIADAS
    async function carregarAtividadesEnviadas() {
        try {
            const response = await fetch("../api/reportApi.php?action=list");
            const atividades = await response.json();
            exibirAtividadesEnviadas(atividades);
        } catch (error) {
            console.error("Erro ao carregar atividades:", error);
        }
    }

    // EXIBIR ATIVIDADES ENVIADAS NA TABELA
    function exibirAtividadesEnviadas(atividades) {
        elements.tabelaBody.innerHTML = "";

        if (atividades.status === "error") {
            elements.tabelaBody.innerHTML = `<tr><td colspan="5">${atividades.message}</td></tr>`;
            return;
        }

        atividades.forEach(atividade => {
            const linha = document.createElement("tr");

            // NOME
            const colunaNome = document.createElement("td");
            colunaNome.textContent = atividade.nome;
            linha.appendChild(colunaNome);

            // ORGAO EMISSOR
            const colunaOrgao = document.createElement("td");
            colunaOrgao.textContent = atividade.orgao;
            linha.appendChild(colunaOrgao);

            // CATEGORIA
            const colunaCategoria = document.createElement("td");
            colunaCategoria.textContent = atividade.categoria;
            linha.appendChild(colunaCategoria);

            // DATA DE REALIZAÇÃO
            const colunaDataRealizacao = document.createElement("td");
            colunaDataRealizacao.textContent = atividade.data_realizacao;
            linha.appendChild(colunaDataRealizacao);

            // STATUS
            const colunaStatus = document.createElement("td");
            colunaStatus.textContent = atividade.status;
            linha.appendChild(colunaStatus);

            // AÇÕES
            const colunaAcoes = document.createElement("td");
            const divAcoes = document.createElement("div");
            divAcoes.classList.add("d-flex");

            const btnCertificado = criarBotao("Certificado", "btn-primary", () => visualizarCertificado(atividade.id), 'eye');
            divAcoes.appendChild(btnCertificado);

            // EDITAR
            if (["Aguardando validacao", "Invalido", "Recategorizacao"].includes(atividade.status)) {
                const btnEditar = criarBotao("Editar", "btn-primary", () => { carregarCategorias(); editarAtividade(atividade.id);}, 'edit');
                divAcoes.appendChild(btnEditar);
            }

            // FEEDBACK
            if (["Invalido", "Recategorizacao"].includes(atividade.status)) {
                const btnFeedback = criarBotao("Feedback", "btn-danger", () => visualizarFeedback(atividade.id));
                divAcoes.appendChild(btnFeedback);
            }

            colunaAcoes.appendChild(divAcoes);
            linha.appendChild(colunaAcoes);
            elements.tabelaBody.appendChild(linha);
        });
    }

    // CRIAR BOTÕES (BOTÕES DE AÇÕES - EDITAR/FEEDBACK)
    function criarBotao(texto, classe, onClick, icone) {
        const button = document.createElement('button');
        button.classList.add('btn', 'btn-sm', classe, 'me-2');

        if (icone) {
            const icon = document.createElement('i');
            icon.classList.add('fa', `fa-${icone}`);
            button.appendChild(icon);
        }

        const textNode = document.createTextNode(` ${texto}`);
        button.appendChild(textNode);
        button.addEventListener('click', onClick);
        return button;
    }

    // ABRIR MODAL PARA EDITAR ATIVIDADE
    async function editarAtividade(id_relatorio) {
        try {
            const response = await fetch(`../api/reportApi.php?action=get_activity&id_relatorio=${id_relatorio}`);
            const atividade = await response.json();

            if (atividade.status !== "error") {
                document.getElementById('edit-id-relatorio').value = atividade.id;
                document.getElementById('edit-nome').value = atividade.nome;
                document.getElementById('edit-data').value = atividade.data_realizacao;
                document.getElementById('edit-descricao').value = atividade.texto_reflexao;
                document.getElementById('edit-categoria').value = atividade.id_categoria;
                document.getElementById('edit-certificado').value = "";

                const editModal = new bootstrap.Modal(document.getElementById('editActivityModal'));
                editModal.show();
            } else {
                console.error(atividade.message);
            }
        } catch (error) {
            console.error("Erro ao carregar a atividade para edição:", error);
        }
    }

    // ENVIAR DADOS - EDITAR ATIVIDADE
    async function salvarEdicaoAtividade(event) {
        event.preventDefault();

        const id_relatorio = document.getElementById('edit-id-relatorio').value;
        const nome = document.getElementById('edit-nome').value;
        const data_realizacao = document.getElementById('edit-data').value;
        const id_categoria = document.getElementById('edit-categoria').value;
        const texto_reflexao = document.getElementById('edit-descricao').value;
        const certificado = document.getElementById('edit-certificado').files[0];

        // SE A ATIVIDADE ESTIVER SENDO RECATEGORIZADA, ATUALIZA PARA AGUARDANDO VALIDACAO
        const status = "Aguardando validacao";

        const formData = new FormData();
        formData.append('id_relatorio', id_relatorio);
        formData.append('nome', nome);
        formData.append('data_realizacao', data_realizacao);
        formData.append('id_categoria', id_categoria);
        formData.append('texto_reflexao', texto_reflexao);
        formData.append('status', status);

        if (certificado) {
            formData.append('certificado', certificado);
        }

        try {
            const response = await fetch('../api/reportApi.php?action=update', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                alert('Atividade atualizada com sucesso!');
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editActivityModal'));
                editModal.hide();
                carregarAtividadesEnviadas();
            } else {
                alert('Erro ao atualizar atividade: ' + result.message);
            }
        } catch (error) {
            console.error('Erro ao atualizar a atividade:', error);
        }
    }

    // VISUALIZAR O FEEDBACK DA ATIVIDADE
    async function visualizarFeedback(id_relatorio) {
        try {
            const response = await fetch(`../api/reportApi.php?action=feedback&id_relatorio=${id_relatorio}`);
            const feedback = await response.json();

            if (feedback.status !== "error") {
                document.getElementById('atividadeNome').textContent = feedback.atividade_nome;
                document.getElementById('feedbackTexto').textContent = feedback.texto_feedback;

                const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
                feedbackModal.show();
            } else {
                console.error(feedback.message);
            }
        } catch (error) {
            console.error("Erro ao carregar o feedback:", error);
        }
    }

    async function visualizarCertificado(idRelatorio) {
        try {
            const response = await fetch(`../api/reportApi.php?action=get_certificate&id_relatorio=${idRelatorio}`);

            // VERIFICAR SE É JSON
            const contentType = response.headers.get("content-type");

            if (contentType && contentType.includes("application/json")) {
                const certificado = await response.json();
                if (certificado.status === "error") {
                    alert(certificado.message);
                }
            } else {
                window.open(`../api/reportApi.php?action=get_certificate&id_relatorio=${idRelatorio}`, '_blank');
            }
        } catch (error) {
            console.error("Erro ao visualizar certificado:", error);
        }
    }

    async function carregarProgressoCategorias() {
        try {
            const response = await fetch("../api/categoryApi.php?action=getProgress");
            const categorias = await response.json();

            const container = document.getElementById('progress-categories-container');
            container.innerHTML = '';

            categorias.forEach(categoria => {
                const card = document.createElement('div');
                card.classList.add('col-md-4', 'mb-4');
                card.innerHTML = `
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">${categoria.nome}</h5>
                            <p>Carga Horária: ${categoria.carga_horaria} horas</p>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped" role="progressbar" style="width: ${Math.min(100, (categoria.horas_realizadas / categoria.carga_horaria) * 100)}%" aria-valuenow="${categoria.horas_realizadas}" aria-valuemin="0" aria-valuemax="${categoria.carga_horaria}">
                                    ${categoria.horas_realizadas} horas
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        } catch (error) {
            console.error("Erro ao carregar as categorias:", error);
        }
    }

    // LOGOUT
    function logout(event) {
        event.preventDefault();
        window.location.href = "../api/logout.php";
    }
});

