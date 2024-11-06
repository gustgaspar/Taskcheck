<?php require 'partials/head.php'; ?>
<div class="header">
    <h1 style="margin: 0; font-size: 24px">TaskCheck - Administrador</h1>
</div>

<div class="container" style="margin-top: 30px">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 style="margin: 0">Gerenciamento de Usuários</h2>
        <button
            id="adicionar-usuario-btn"
            class="btn btn-add-user"
            data-bs-toggle="modal"
            data-bs-target="#adicionarUsuarioModal"
        >
            <i class="fas fa-user-plus"></i> Adicionar Usuário
        </button>
    </div>

    <div id="user-list-container" class="card shadow-lg p-4 mx-auto">
        <div class="table-responsive">
            <table id="lista_usuarios" class="table table-hover">
                <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Email</th>
                    <th>Senha</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <!-- DADOS INSERIDOS AQUI -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL ADICIONAR -->
<div
    class="modal fade"
    id="adicionarUsuarioModal"
    tabindex="-1"
    aria-labelledby="adicionarUsuarioModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adicionarUsuarioModalLabel">
                    Adicionar Usuário
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <form id="form-adicionar-usuario">
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Usuário</label>
                        <select class="form-select" id="tipo" required>
                            <option value="">Selecione o tipo</option>
                            <option value="aluno">Aluno</option>
                            <option value="professor">Professor</option>
                            <option value="coordenador">Coordenador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nome-usuario" class="form-label">Nome</label>
                        <input
                            type="text"
                            class="form-control"
                            id="nome-usuario"
                            required
                        />
                    </div>
                    <div class="mb-3">
                        <label for="email-usuario" class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email-usuario"
                            required
                        />
                    </div>
                    <div class="mb-3">
                        <label for="senha-usuario" class="form-label">Senha</label>
                        <input
                            type="password"
                            class="form-control"
                            id="senha-usuario"
                            required
                        />
                    </div>
                    <!-- CAMPOS ALUNO -->
                    <div id="aluno-fields">
                        <div class="mb-3">
                            <label for="matricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="matricula" />
                        </div>
                        <div class="mb-3">
                            <label for="curso-aluno" class="form-label">Curso</label>
                            <select class="form-select" id="curso-aluno">
                                <!-- CURSOS -->
                            </select>
                        </div>
                    </div>
                    <!-- CAMPOS COORDENADOR -->
                    <div id="coordenador-fields">
                        <div class="mb-3">
                            <label for="curso-coordenador" class="form-label"
                            >Curso Responsável</label
                            >
                            <select class="form-select" id="curso-coordenador">
                                <!-- CURSOS-->
                            </select>
                        </div>
                    </div>
                    <!-- CAMPOS PROFESSOR -->
                    <div id="professor-fields">
                        <div class="mb-3">
                            <label for="curso-professor" class="form-label"
                            >Curso</label
                            >
                            <select class="form-select" id="curso-professor">
                                <!-- CURSOS-->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Adicionar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ATUALIZAR -->
<div
    class="modal fade"
    id="atualizarUsuarioModal"
    tabindex="-1"
    aria-labelledby="atualizarUsuarioModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="atualizarUsuarioModalLabel">
                    Atualizar Usuário
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <form id="form-atualizar-usuario">
                    <div class="mb-3">
                        <label for="atualizar-nome" class="form-label">Nome</label>
                        <input
                            type="text"
                            class="form-control"
                            id="atualizar-nome"
                            placeholder="Informe o nome do usuário"
                            required
                        />
                    </div>
                    <div class="mb-3">
                        <label for="atualizar-email" class="form-label">E-mail</label>
                        <input
                            type="email"
                            class="form-control"
                            id="atualizar-email"
                            placeholder="Informe o e-mail do usuário"
                            required
                        />
                    </div>
                    <div class="mb-3">
                        <label for="atualizar-senha" class="form-label">Senha</label>
                        <input
                            type="password"
                            class="form-control"
                            id="atualizar-senha"
                            placeholder="Informe a nova senha"
                            required
                        />
                    </div>
                    <input type="hidden" id="atualizar-id-usuario" />
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require 'partials/footer.php' ?>
