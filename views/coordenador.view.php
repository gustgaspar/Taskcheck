<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] != 'coordenador') {
    header("Location: /");
    exit();
}
?>

<?php require 'partials/head.php'; ?>
<?php require 'partials/header.php'; ?>
<?php require 'partials/sidebar.php'; ?>

<div class="content" id="content">
    <!-- CARD LISTAR CATEGORIAS -->
    <div class="container mt-5" id="categories-card" style="display: none;">
        <div class="card p-4">
            <h2 class="card-title mb-4">Categorias</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Nome da Categoria</th>
                        <th>Carga Horária</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody id="categorias-list">
                    <!-- CATEGORIAS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FORMULÁRIO ADICIONAR CATEGORIA -->
    <div class="container mt-5" id="add-category-form" style="display: none;">
        <div class="card shadow-sm p-4 mx-auto" style="max-width: 1200px;">
            <h3 class="text-center mb-4">Adicionar Categoria</h3>
            <form id="form-adicionar-categoria">
                <div class="mb-3">
                    <label for="nome-categoria" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome-categoria" placeholder="Digite o nome da categoria" required>
                </div>
                <div class="mb-3">
                    <label for="carga-horaria" class="form-label">Carga Horária</label>
                    <div class="d-flex align-items-center">
                        <input type="range" class="form-range flex-grow-1" id="carga-horaria" min="0" max="100">
                        <div class="input-group ms-3" style="max-width: 150px;">
                            <input type="text" class="form-control" id="horas" readonly>
                            <span class="input-group-text">Horas</span>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" rows="4" placeholder="Digite a descrição" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Adicionar Categoria</button>
            </form>
        </div>
    </div>

    <!-- MODAL EDITAR CATEGORIA -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Editar Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar-categoria">
                        <input type="hidden" id="edit-category-id">
                        <div class="mb-3">
                            <label for="edit-nome-categoria" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="edit-nome-categoria" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-carga-horaria" class="form-label">Carga Horária</label>
                            <div class="d-flex align-items-center">
                                <input type="range" class="form-range flex-grow-1" id="edit-carga-horaria" min="0" max="100">
                                <div class="input-group ms-3" style="max-width: 150px;">
                                    <input type="text" class="form-control" id="edit-horas" readonly>
                                    <span class="input-group-text">Horas</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="edit-descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="edit-descricao" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'partials/footer.php'; ?>
