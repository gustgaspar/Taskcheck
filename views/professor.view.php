<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] != 'professor') {
    header("Location: /");
    exit();
}
?>
<?php require 'partials/head.php'; ?>
<?php require 'partials/header.php'; ?>
<?php require 'partials/sidebar.php'; ?>
<div class="content" id="content">
    <!-- AVISO ALTA DEMANDA -->
    <div id="avisoAltaDemanda" class="alert alert-warning" style="display: none;">
        <strong>Aviso:</strong> Existem mais de 50 relatórios aguardando validação. Priorize o processo para reduzir a demanda.
    </div>

    <!-- RELATÓRIOS SUBMETIDOS -->
    <div class="container mt-5" id="sent-reports-card" style="display: none;">
        <div class="card p-4">
            <h2 class="card-title mb-4">Relatórios Submetidos</h2>

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
                        <th>Nome do Relatório</th>
                        <th>Curso</th>
                        <th>Categoria</th>
                        <th>Aluno</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DETALHES -->
    <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalhesModalLabel">Detalhes do Relatório</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Data de Realização:</strong> <span id="detalheDataRealizacao"></span></p>
                    <p><strong>Data de Envio:</strong> <span id="detalheDataEnvio"></span></p>
                    <p><strong>Categoria:</strong> <span id="detalheCategoria"></span></p>
                    <p><strong>Nome:</strong> <span id="detalheNome"></span></p>
                    <p><strong>Reflexão:</strong> <span id="detalheReflexao"></span></p>
                    <!-- CAMPO FEEDBACK -->
                    <div id="feedbackField" style="display: none;">
                        <label for="feedbackText" class="form-label">Feedback:</label>
                        <textarea id="feedbackText" class="form-control" rows="3"></textarea>
                    </div>
                    <!-- CAMPO JUSTIFICATIVA -->
                    <div id="reversaoField" style="display: none;">
                        <label for="justificativaReversao" class="form-label">Justificativa para Reversão:</label>
                        <textarea id="justificativaReversao" class="form-control" rows="3"></textarea>
                    </div>
                    <!-- CAMPO HORAS VALIDADAS -->
                    <div id="horasField" style="display: none;">
                        <label for="horasValidacao" class="form-label">Carga horária validada:</label>
                        <input type="number" id="horasValidacao" class="form-control">
                    </div>

                    <!-- FEEDBACK ATUAL -->
                    <button id="btnFeedbackAtual" class="btn btn-secondary mt-3 mb-3" style="display: none;">Feedback Atual</button>
                    <div id="feedbackAtual" style="display: none;">
                        <div class="accordion" id="accordionFeedbackAtual"></div>
                    </div>

                    <!-- HISTÓRICO DE FEEDBACK -->
                    <button id="btnHistoricoFeedback" class="btn btn-secondary mt-3 mb-3" style="display: none;">Histórico Feedback</button>
                    <div id="feedbackHistorico" style="display: none;">
                        <div class="accordion" id="accordionFeedbackHistorico"></div>
                    </div>

                    <!-- HISTÓRICO DE ALTERAÇÕES -->
                    <button id="btnHistoricoAlteracoes" class="btn btn-secondary mt-3 mb-3" style="display: none;">Histórico de Alterações</button>
                    <div id="alteracoesHistorico" style="display: none;">
                        <div class="accordion" id="accordionAlteracoesHistorico"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCertificado" class="btn btn-secondary">Certificado</button>
                    <button id="btnValidar" class="btn btn-success">Validar</button>
                    <button id="btnInvalidar" class="btn btn-danger">Invalidar</button>
                    <button id="btnReverter" class="btn btn-warning" style="display: none;">Reverter Validação</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require 'partials/footer.php'; ?>
