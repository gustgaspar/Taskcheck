<?php
session_start();

if (isset($_SESSION['id_usuario'])) {
    switch ($_SESSION['tipo']) {
        case 'aluno':
            header("Location: /aluno");
            exit();
        case 'professor':
            header("Location: /professor");
            exit();
        case 'coordenador':
            header("Location: /coordenador");
            exit();
    }
}
?>

<?php require 'partials/head.php' ?>
<div class="container">
    <div class="card shadow-lg p-4 mx-auto mb-4" style="max-width: 450px">
        <div class="text-center">
            <img
                    src="../assets/img/logo_pucpr.png"
                    alt="PUCPR Grupo Marista"
                    class="mt-3 mb-4 w-100"
            />
            <h2 class="mb-4">TaskCheck</h2>
        </div>
        <form id="login-form">
            <div class="form-floating mb-3">
                <input
                        type="email"
                        class="form-control"
                        id="email"
                        placeholder="Informe seu e-mail"
                />
                <label for="email">E-mail</label>
            </div>
            <div class="form-floating mb-4 position-relative">
                <input
                        type="password"
                        class="form-control"
                        id="senha"
                        placeholder="Informe sua senha"
                />
                <label for="senha">Senha</label>
                <span class="position-absolute top-50 end-0 translate-middle-y me-3" id="toggle-password" style="cursor: pointer;">
                    <i class="fas fa-eye" id="eye-icon"></i>
                </span>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">
                Entrar
            </button>
        </form>
        <div id="response-message" class="mt-3"></div>
    </div>
</div>
<?php require 'partials/footer.php' ?>
