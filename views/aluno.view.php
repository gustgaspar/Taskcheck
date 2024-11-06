<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] != 'aluno') {
    header("Location: /");
    exit();
}
?>
<?php require 'partials/head.php'; ?>
<?php require 'partials/header.php'; ?>
<?php require 'partials/sidebar.php'; ?>
<div class="content" id="content">
    <!-- ADICIONAR ATIVIDADE -->
    <div class="container mt-5" id="activity-form" style="display: none; max-width: 1200px">
        <div class="card p-4">
            <h3 class="card-title mb-4">Relatório de Atividade</h3>
            <form novalidate>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="data" class="form-label">Data de realização</label>
                        <input type="text" class="form-control" id="data" placeholder="DD/MM/AAAA" pattern="\d{2}/\d{2}/\d{4}" required />
                        <div class="invalid-feedback">Por favor, insira uma data válida.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select id="categoria" class="form-select" required>
                            <option selected>Selecionar categoria</option>
                            <!-- CATEGORIAS -->
                        </select>
                        <div class="invalid-feedback">Por favor, selecione uma categoria.</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da atividade</label>
                    <input type="text" class="form-control" id="nome" placeholder="Informe o nome" required />
                    <div class="invalid-feedback">Por favor, insira o nome da atividade.</div>
                </div>
                <div class="mb-3">
                    <label for="orgao" class="form-label">Nome do orgao</label>
                    <input type="text" class="form-control" id="orgao" placeholder="Informe o orgao" required />
                    <div class="invalid-feedback">Por favor, insira o nome do orgao.</div>
                </div>
                <div class="mb-3">
                    <label for="reflexao" class="form-label">Reflexão</label>
                    <textarea class="form-control" id="reflexao" rows="3" placeholder="Informe a reflexão" required></textarea>
                    <div class="invalid-feedback">Por favor, insira a reflexão.</div>
                </div>
                <div class="mb-3">
                    <label for="certificado" class="form-label">Anexar certificado</label>
                    <input type="file" class="form-control" id="certificado" required />
                    <div class="invalid-feedback">Por favor, anexe um arquivo PDF válido.</div>
                </div>
                <div id="response-message"></div>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        </div>
    </div>

    <!-- ATIVIDADES SUBMETIDAS -->
    <div class="container mt-5" id="sent-activities-card" style="display: none;">
        <div class="card p-4">
            <h2 class="card-title mb-4">Atividades submetidas</h2>

            <div class="row mb-4">
                <div class="col-md-3">
                    <select class="form-select" id="filtrarAtividades">
                        <!-- CATEGORIAS -->
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Orgao Emissor</th>
                        <th>Categoria</th>
                        <th>Data de Realização</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PROGRESSO POR CATEGORIA -->
    <div class="container mt-5" id="progress-categories" style="display: none;">
        <h2>Progresso por Categoria</h2>
        <div class="row" id="progress-categories-container">
            <!-- CARDS CATEGORIAS PROGRESSO -->
        </div>
    </div>

    <!-- MODAL FEEDBACK -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="mb-3">Atividade complementar: <span id="atividadeNome"></span></h6>
                    <p id="feedbackTexto"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDITAR ATIVIDADE -->
<div class="modal fade" id="editActivityModal" tabindex="-1" aria-labelledby="editActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editActivityModalLabel">Editar Atividade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-activity-form">
                    <input type="hidden" id="edit-id-relatorio" />
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-data" class="form-label">Data de realização</label>
                            <input type="text" class="form-control" id="edit-data" placeholder="DD/MM/AAAA" />
                        </div>
                        <div class="col-md-6">
                            <label for="edit-categoria" class="form-label">Categoria</label>
                            <select id="edit-categoria" class="form-select">
                                <option selected>Selecionar categoria</option>
                                <!-- CATEGORIAS -->
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit-nome" placeholder="Informe o nome" />
                    </div>
                    <div class="mb-3">
                        <label for="edit-descricao" class="form-label">Reflexão</label>
                        <textarea class="form-control" id="edit-descricao" rows="3" placeholder="Informe a reflexão"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-certificado" class="form-label">Certificado</label>
                        <input type="file" class="form-control" id="edit-certificado" accept="application/pdf"/>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'partials/footer.php'; ?>
