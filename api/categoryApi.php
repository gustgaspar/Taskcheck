<?php
session_start();

global $connection;
include '../database/database.php';
include '../database/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : null;

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['tipo'])) {
    json_return(["status" => "error", "message" => "Usuário não autenticado."]);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$tipo_usuario = $_SESSION['tipo'];

if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    switch ($action) {
        case 'add':
            if (isset($data['nome'], $data['carga_horaria'], $data['descricao'])) {
                adicionarCategoria($data);
            } else {
                json_return(["status" => "error", "message" => "Dados incompletos."]);
            }
            break;
        case 'update':
            if (isset($data['id'], $data['nome'], $data['carga_horaria'], $data['descricao'])) {
                atualizarCategoria($data);
            } else {
                json_return(["status" => "error", "message" => "Dados incompletos."]);
            }
            break;
        default:
            json_return(["status" => "error", "message" => "Ação não encontrada."]);
            break;
    }
} elseif ($method === "GET") {
    switch ($action) {
        case 'list':
            obterCategorias($id_usuario, $tipo_usuario);
            break;
        case 'get':
            if (isset($_GET['id'])) {
                obterCategoria($_GET['id']);
            } else {
                json_return(["status" => "error", "message" => "ID da categoria não fornecido."]);
            }
            break;
        case 'getProgress':
            getProgress($id_usuario);
            break;
        case 'delete':
            if (isset($_GET['id'])) {
                excluirCategoria($_GET['id']);
            } else {
                json_return(["status" => "error", "message" => "ID da categoria não fornecido."]);
            }
            break;
        default:
            json_return(["status" => "error", "message" => "Ação não encontrada."]);
            break;
    }
} else {
    json_return(["status" => "error", "message" => "Método não suportado"]);
}

// ADICIONAR CATEGORIA
function adicionarCategoria($dados) {
    global $connection;
    $id_coordenador = $_SESSION['id_usuario'];

    $nome = mysqli_real_escape_string($connection, $dados['nome']);
    $carga_horaria = intval($dados['carga_horaria']);
    $descricao = mysqli_real_escape_string($connection, $dados['descricao']);

    // CURSO DO COORDENADOR
    $queryCurso = "SELECT id_curso_responsavel FROM coordenador WHERE id_usuario = $id_coordenador";
    $cursoResult = consultar_dado($queryCurso);

    if (is_array($cursoResult) && count($cursoResult) > 0) {
        $id_curso = $cursoResult[0]['id_curso_responsavel'];

        $colunas = "nome, carga_horaria, descricao, id_curso";
        $valores = "'$nome', $carga_horaria, '$descricao', $id_curso";

        $resultado = inserir_dado("categoria", $colunas, $valores);

        if ($resultado['status'] === 'success') {
            json_return(["status" => "success", "message" => "Categoria adicionada com sucesso"]);
        } else {
            json_return(["status" => "error", "message" => "Erro ao adicionar categoria: " . $resultado['message']]);
        }
    } else {
        json_return(["status" => "error", "message" => "Curso não encontrado para o coordenador."]);
    }
}

// OBTER UMA CATEGORIA ESPECÍFICA
function obterCategoria($id_categoria) {
    $query = "SELECT id, nome, carga_horaria, descricao FROM categoria WHERE id = $id_categoria";
    $categoria = consultar_dado($query);

    if (is_array($categoria) && count($categoria) > 0) {
        json_return($categoria[0]);
    } else {
        json_return(["status" => "error", "message" => "Categoria não encontrada."]);
    }
}

// EXCLUIR CATEGORIAS E ATUALIZAR ATIVIDADES ASSOCIADAS
function excluirCategoria($id_categoria) {
    global $connection;

    // REMOVER REFERÊNCIA DA CATEGORIA
    $atributosAtividades = "id_categoria = NULL, status = 'Recategorizacao', horas_validadas = NULL";
    $condicaoAtividades = "id_categoria = $id_categoria";

    $resultadoAtualizarAtividades = atualizar_dado("relatorio_atividade", $atributosAtividades, $condicaoAtividades);

    if ($resultadoAtualizarAtividades['status'] !== 'success') {
        json_return(["status" => "error", "message" => "Erro ao atualizar atividades associadas: " . $resultadoAtualizarAtividades['message']]);
    }

    // FEEDBACK RECATEGORIZACAO
    $feedbackText = "Atividade pendente de recategorização";

    // CRIA OS VALORES DO INSERT
    $queryInsertFeedback = "
        SELECT id
        FROM relatorio_atividade
        WHERE id_categoria IS NULL
    ";
    $atividadesRecategorizadas = consultar_dado($queryInsertFeedback);

    if (count($atividadesRecategorizadas) > 0) {
        foreach ($atividadesRecategorizadas as $atividade) {
            $colunasFeedback = "texto_feedback, id_relatorio, data_envio, id_professor";
            $valoresFeedback = "'$feedbackText', {$atividade['id']}, NOW(), NULL";

            $resultadoInsertFeedback = inserir_dado("feedback", $colunasFeedback, $valoresFeedback);

            if ($resultadoInsertFeedback['status'] !== 'success') {
                json_return(["status" => "error", "message" => "Erro ao adicionar feedback às atividades recategorizadas: " . $resultadoInsertFeedback['message']]);
            }
        }
    }

    // EXCLUIR CATEGORIA
    $resultadoExcluirCategoria = deletar_dado("categoria", "id = $id_categoria");

    if ($resultadoExcluirCategoria['status'] === 'success') {
        json_return(["status" => "success", "message" => "Categoria excluída com sucesso e atividades marcadas para recategorização."]);
    } else {
        json_return(["status" => "error", "message" => $resultadoExcluirCategoria['message']]);
    }
}

// OBTER CATEGORIAS - ALUNO/COORDENADOR/PROFESSOR
function obterCategorias($id_usuario, $tipo_usuario) {
    global $connection;

    if ($tipo_usuario === 'aluno') {
        $queryCurso = "SELECT id_curso FROM aluno WHERE id_usuario = $id_usuario";
        $cursoResult = consultar_dado($queryCurso);

        if (is_array($cursoResult) && count($cursoResult) > 0) {
            $id_curso = $cursoResult[0]['id_curso'];

            $queryCategorias = "SELECT id, nome, carga_horaria FROM categoria WHERE id_curso = $id_curso";
            $categorias = consultar_dado($queryCategorias);

            json_return($categorias);
        } else {
            json_return(["status" => "error", "message" => "Curso do aluno não encontrado."]);
        }
    } elseif ($tipo_usuario === 'coordenador') {
        $queryCursoCoordenador = "SELECT id_curso_responsavel FROM coordenador WHERE id_usuario = $id_usuario";
        $cursoResult = consultar_dado($queryCursoCoordenador);

        if (is_array($cursoResult) && count($cursoResult) > 0) {
            $id_curso = $cursoResult[0]['id_curso_responsavel'];

            $queryCategorias = "SELECT id, nome, carga_horaria FROM categoria WHERE id_curso = $id_curso";
            $categorias = consultar_dado($queryCategorias);

            json_return($categorias);
        } else {
            json_return(["status" => "error", "message" => "Curso não encontrado para o coordenador."]);
        }
    } elseif ($tipo_usuario === 'professor') {
        $queryCursosProfessor = "SELECT id_curso FROM professor_curso WHERE id_professor = $id_usuario";
        $cursosResult = consultar_dado($queryCursosProfessor);

        if (is_array($cursosResult) && count($cursosResult) > 0) {
            $ids_cursos = array_column($cursosResult, 'id_curso');
            $ids_cursos_list = implode(",", $ids_cursos);

            $queryCategorias = "SELECT id, nome, carga_horaria FROM categoria WHERE id_curso IN ($ids_cursos_list)";
            $categorias = consultar_dado($queryCategorias);

            json_return($categorias);
        } else {
            json_return(["status" => "error", "message" => "Nenhum curso associado ao professor encontrado."]);
        }
    } else {
        json_return(["status" => "error", "message" => "Usuário não autorizado."]);
    }
}


function atualizarCategoria($dados) {
    global $connection;

    $id_categoria = intval($dados['id']);
    $nome = mysqli_real_escape_string($connection, $dados['nome']);
    $carga_horaria = intval($dados['carga_horaria']);
    $descricao = mysqli_real_escape_string($connection, $dados['descricao']);

    $atributos = "nome = '$nome', carga_horaria = $carga_horaria, descricao = '$descricao'";
    $condicao = "id = $id_categoria";

    $resultado = atualizar_dado('categoria', $atributos, $condicao);

    if ($resultado['status'] === 'success') {
        json_return(["status" => "success", "message" => "Categoria atualizada com sucesso"]);
    } else {
        json_return(["status" => "error", "message" => "Erro ao atualizar categoria: " . $resultado['message']]);
    }
}

// PROGRESSO HORAS USUARIO ALUNO E CATEGORIA
function getProgress($id_usuario) {
    $query = "
        SELECT 
            c.id, 
            c.nome, 
            c.carga_horaria, 
            IFNULL(SUM(r.horas_validadas), 0) AS horas_realizadas
        FROM categoria c
        LEFT JOIN relatorio_atividade r ON c.id = r.id_categoria AND r.id_aluno = $id_usuario AND r.status = 'Valido'
        INNER JOIN aluno a ON a.id_curso = c.id_curso
        WHERE a.id_usuario = $id_usuario
        GROUP BY c.id, c.nome, c.carga_horaria
    ";

    $categorias = consultar_dado($query);
    json_return($categorias);

}
?>
