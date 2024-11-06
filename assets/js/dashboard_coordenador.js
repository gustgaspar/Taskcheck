document.addEventListener("DOMContentLoaded", () => {
    const elements = {
        toggleSidebarButton: document.getElementById("toggleSidebar"),
        sidebar: document.getElementById("sidebar"),
        content: document.getElementById("content"),
        categoriasList: document.getElementById('categorias-list'),
        formEditarCategoria: document.getElementById('form-editar-categoria'),
        formAdicionarCategoria: document.getElementById('form-adicionar-categoria'),
        editRangeCargaHoraria: document.getElementById('edit-carga-horaria'),
        editInputHoras: document.getElementById('edit-horas'),
        addRangeCargaHoraria: document.getElementById('carga-horaria'),
        addInputHoras: document.getElementById('horas'),
        viewCategoriesButton: document.getElementById("view-categories-card"),
        addCategoryButton: document.getElementById("add-category-btn"),
        logoutButton: document.getElementById("logout-btn"),
    };

    // INICIALIZA EVENTOS AO CARREGAR A PÁGINA
    init();

    // CONFIG DOS EVENTOS
    function init() {
        // CONFIG SINCRONIZAR INPUT DE HORAS COM O "RANGE" - FORM ADD E MODAL EDIT
        syncInputWithRange(elements.editRangeCargaHoraria, elements.editInputHoras);
        syncInputWithRange(elements.addRangeCargaHoraria, elements.addInputHoras);

        // CONFIG EVENTOS PRINCIPAIS
        elements.toggleSidebarButton.addEventListener("click", toggleSidebar);
        elements.viewCategoriesButton.addEventListener("click", mostrarCardCategorias);
        elements.addCategoryButton.addEventListener("click", mostrarFormularioAdicionarCategoria);

        // CONFIG ENVIAR FORMULÁRIOS - FORM ADD E MODAL EDIT
        elements.formAdicionarCategoria.addEventListener('submit', adicionarCategoria);
        elements.formEditarCategoria.addEventListener('submit', salvarEdicaoCategoria);

        // LOGOUT
        elements.logoutButton.addEventListener("click", logout);

        // CARREGAR CATEGORIAS
        carregarCategorias();
    }

    // SINCRONIZAR INPUT E RANGE DE HORAS (CARGA HORÁRIA)
    function syncInputWithRange(rangeElement, inputElement) {
        inputElement.value = rangeElement.value;
        rangeElement.addEventListener('input', () => {
            inputElement.value = rangeElement.value;
        });
    }

    // MOSTRAR/ESCONDER SIDEBAR
    function toggleSidebar() {
        elements.sidebar.classList.toggle("hidden");
        elements.content.classList.toggle("sidebar-hidden");
    }

    // MOSTRAR CARD DE CATEGORIAS
    function mostrarCardCategorias(event) {
        event.preventDefault();
        document.getElementById('categories-card').style.display = 'block';
        document.getElementById('add-category-form').style.display = 'none';
        carregarCategorias();
    }

    // MOSTRAR FORMULÁRIO DE ADICIONAR CATEGORIA
    function mostrarFormularioAdicionarCategoria(event) {
        event.preventDefault();
        document.getElementById('add-category-form').style.display = 'block';
        document.getElementById('categories-card').style.display = 'none';
    }

    // CARREGAR CATEGORIAS
    async function carregarCategorias() {
        try {
            const response = await fetch("../api/categoryApi.php?action=list");
            const categorias = await response.json();
            exibirCategorias(categorias);
        } catch (error) {
            console.error("Erro ao carregar categorias:", error);
        }
    }

    // EXIBIR CATEGORIAS NA TABELA
    function exibirCategorias(categorias) {
        elements.categoriasList.innerHTML = '';

        if (categorias.status === "error") {
            elements.categoriasList.innerHTML = `<tr><td colspan="3">${categorias.message}</td></tr>`;
            return;
        }

        categorias.forEach(categoria => {
            const row = document.createElement('tr');

            // NOME
            const nomeCell = document.createElement('td');
            nomeCell.textContent = categoria.nome;
            row.appendChild(nomeCell);

            // CARGA HORÁRIA
            const cargaHorariaCell = document.createElement('td');
            cargaHorariaCell.textContent = `${categoria.carga_horaria} horas`;
            row.appendChild(cargaHorariaCell);

            // AÇÕES (EDITAR/EXCLUIR)
            const acoesCell = document.createElement('td');
            const divAcoes = document.createElement('div');
            divAcoes.classList.add('d-flex');

            const btnEditar = criarBotao('Editar', 'btn-primary', () => abrirModalEdicaoCategoria(categoria.id));
            const btnExcluir = criarBotao('Excluir', 'btn-danger', () => excluirCategoria(categoria.id));

            divAcoes.appendChild(btnEditar);
            divAcoes.appendChild(btnExcluir);
            acoesCell.appendChild(divAcoes);
            row.appendChild(acoesCell);

            elements.categoriasList.appendChild(row);
        });
    }

    // CRIAR BOTÕES (BOTÕES DE AÇÕES - EDITAR/EXCLUIR)
    function criarBotao(texto, classe, onClick) {
        const button = document.createElement('button');
        button.classList.add('btn', 'btn-sm', classe, 'me-2');
        button.textContent = texto;
        button.addEventListener('click', onClick);
        return button;
    }

    // ADICIONAR CATEGORIA
    async function adicionarCategoria(event) {
        event.preventDefault();

        const categoriaData = {
            nome: document.getElementById('nome-categoria').value,
            carga_horaria: document.getElementById('carga-horaria').value,
            descricao: document.getElementById('descricao').value,
        };

        try {
            const response = await fetch('../api/categoryApi.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(categoriaData)
            });
            const data = await response.json();

            if (data.status === 'success') {
                alert('Categoria adicionada com sucesso!');
                elements.formAdicionarCategoria.reset();
                syncInputWithRange(elements.addRangeCargaHoraria, elements.addInputHoras);
                mostrarCardCategorias();
            } else {
                alert('Erro ao adicionar categoria: ' + data.message);
            }
        } catch (error) {
            console.error('Erro:', error);
        }
    }

    // EDITAR CATEGORIA (ABRIR MODAL)
    async function abrirModalEdicaoCategoria(id_categoria) {
        try {
            const response = await fetch(`../api/categoryApi.php?action=get&id=${id_categoria}`);
            const categoria = await response.json();

            if (categoria.status !== 'error') {
                document.getElementById('edit-category-id').value = categoria.id;
                document.getElementById('edit-nome-categoria').value = categoria.nome;
                document.getElementById('edit-carga-horaria').value = categoria.carga_horaria;
                document.getElementById('edit-horas').value = categoria.carga_horaria;
                document.getElementById('edit-descricao').value = categoria.descricao;

                const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                editModal.show();
            } else {
                alert('Erro ao carregar categoria: ' + categoria.message);
            }
        } catch (error) {
            console.error('Erro ao carregar categoria:', error);
        }
    }

    // SALVAR - EDIÇÃO DE CATEGORIA
    async function salvarEdicaoCategoria(event) {
        event.preventDefault();

        const categoriaData = {
            id: document.getElementById('edit-category-id').value,
            nome: document.getElementById('edit-nome-categoria').value,
            carga_horaria: document.getElementById('edit-carga-horaria').value,
            descricao: document.getElementById('edit-descricao').value,
        };

        try {
            const response = await fetch('../api/categoryApi.php?action=update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(categoriaData)
            });
            const data = await response.json();

            if (data.status === 'success') {
                alert('Categoria atualizada com sucesso!');
                carregarCategorias();
                bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
            } else {
                alert('Erro ao atualizar categoria: ' + data.message);
            }
        } catch (error) {
            console.error('Erro ao atualizar categoria:', error);
        }
    }

    // EXCLUIR CATEGORIA
    async function excluirCategoria(id_categoria) {
        if (confirm('Tem certeza que deseja excluir essa categoria?')) {
            try {
                const response = await fetch(`../api/categoryApi.php?action=delete&id=${id_categoria}`, { method: 'GET' });
                const data = await response.json();

                if (data.status === 'success') {
                    alert('Categoria excluída com sucesso!');
                    carregarCategorias();
                } else {
                    alert('Erro ao excluir categoria: ' + data.message);
                }
            } catch (error) {
                console.error('Erro ao excluir categoria:', error);
            }
        }
    }

    // LOGOUT
    function logout(event) {
        event.preventDefault();
        window.location.href = "../api/logout.php";
    }
});
