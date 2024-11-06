<?php
global $connection;
include '../database/database.php';
include '../database/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

$action = isset($_GET['action']) ? $_GET['action'] : null;

if($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    switch($action) {
        case 'add':
            adicionarUsuario($data);
            break;
        case 'update':
            atualizarUsuario($data);
            break;
        default:
            json_return(["status" => "error", "message" => "Ação não encontrada."]);
            break;
    }
} elseif ($method === "GET") {
    switch($action) {
        case 'list':
            listarUsuarios();
            break;
        case 'delete':
            if(isset($_GET['id'])) {
                excluirUsuario($_GET['id']);
            } else {
                json_return(["status" => "error", "message" => "ID não encontrado."]);
            }
            break;
        case 'cursos':
            obterCursos();
            break;
        default:
            json_return(["status" => "error", "message" => "Ação não encontrada."]);
            break;
    }
} else {
    json_return(["status" => "error", "message" => "Método não suportado"]);
}

function adicionarUsuario($data) {
    global $connection;

    $tipo = $data['tipo'];
    $nome = mysqli_real_escape_string($connection, $data['nome']);
    $email = mysqli_real_escape_string($connection, $data['email']);

    $senha = password_hash($data['senha'], PASSWORD_DEFAULT);

    $colunas = "nome, email, senha, tipo";
    $valores = "'$nome', '$email', '$senha', '$tipo'";

    $usuario_result = inserir_dado("usuario", $colunas, $valores);

    if ($usuario_result['status'] === 'success') {
        $id_usuario = $usuario_result['insert_id'];

        switch ($tipo) {
            case 'aluno':
                $matricula = mysqli_real_escape_string($connection, $data['matricula']);
                $id_curso = intval($data['id_curso']);
                $colunas_aluno = "id_usuario, matricula, id_curso";
                $valores_aluno = "$id_usuario, '$matricula', $id_curso";
                inserir_dado("aluno", $colunas_aluno, $valores_aluno);
                break;
            case 'coordenador':
                $id_curso_responsavel = intval($data['id_curso_responsavel']);
                $colunas_coordenador = "id_usuario, id_curso_responsavel";
                $valores_coordenador = "$id_usuario, $id_curso_responsavel";
                inserir_dado("coordenador", $colunas_coordenador, $valores_coordenador);
                break;
            case 'professor':
                inserir_dado("professor", "id_usuario", $id_usuario);
                $id_curso_professor = intval($data['id_curso_professor']);
                $colunas_professor = "id_professor, id_curso";
                $valores_professor = "'$id_usuario', '$id_curso_professor'";
                inserir_dado("professor_curso", $colunas_professor, $valores_professor);
                break;
            default:
                break;
        }
        json_return(["status" => "success", "message" => "Usuário adicionado com sucesso."]);
    } else {
        json_return(["status" => "error", "message" => $usuario_result['message']]);
    }
}

function atualizarUsuario($data) {
    global $connection;

    if (isset($data['id'], $data['nome'], $data['email'], $data['senha'])) {
        $id = mysqli_real_escape_string($connection, $data['id']);
        $nome = mysqli_real_escape_string($connection, $data['nome']);
        $email = mysqli_real_escape_string($connection, $data['email']);

        $senha = password_hash($data['senha'], PASSWORD_DEFAULT);

        $atributos = "nome = '$nome', email = '$email', senha = '$senha'";
        $result = atualizar_dado('usuario', $atributos, "id = $id");

        if ($result['status'] === 'success') {
            json_return(["status" => "success", "message" => "Usuário atualizado com sucesso."]);
        } else {
            json_return(["status" => "error", "message" => $result['message']]);
        }
    } else {
        json_return(["status" => "error", "message" => "Dados incompletos."]);
    }
}

function listarUsuarios() {
    $usuarios = consultar_dado("SELECT * FROM usuario");
    json_return($usuarios);
}

function excluirUsuario($id) {
    $condicao = "id=$id";
    $resultado = deletar_dado("usuario", $condicao);

    if ($resultado['status'] === 'success') {
        json_return(["status" => "success", "message" => "Usuário excluído com sucesso"]);
    } else {
        json_return(["status" => "error", "message" => $resultado['message']]);
    }
}

function obterCursos() {
    $cursos = consultar_dado("SELECT id, nome FROM curso");
    json_return($cursos);
}
mysqli_close($connection);
?>