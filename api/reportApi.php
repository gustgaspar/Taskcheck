<?php
session_start();

global $connection;
include '../database/database.php';
include '../database/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : null;

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['tipo'])/* || $_SESSION['tipo'] !== 'aluno'*/) {
    json_return(["status" => "error", "message" => "Usuário não autenticado ou sem permissão."]);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

if ($method === "POST") {
    switch ($action) {
        case 'add':
            // VERIFICAR NECESSIDADE DE ISSET POIS JÁ É TRATADO EM adicionarAtividade($dados) --------- LEMBRARRRRRRRRRRRRRRRRR
            if (isset($_POST['nome']) && isset($_POST['data_realizacao']) && isset($_POST['id_categoria']) && isset($_POST['texto_reflexao'])) {
                adicionarAtividade($_POST);
            } else {
                json_return(["status" => "error", "message" => "Dados incompletos."]);
            }
            break;
        case 'update':
            if (isset($_POST['id_relatorio'])) {
                atualizarAtividade($_POST);
            } else {
                json_return(["status" => "error", "message" => "ID do relatório não fornecido."]);
            }
            break;
        case 'validate':
            if ($_SESSION['tipo'] === "professor" && isset($_POST['id_relatorio'])) {
                validarRelatorio($_POST);
            } else {
                json_return(["status" => "error", "message" => "Usuário não autorizado."]);
            }
            break;
        case 'invalidate':
            if ($_SESSION['tipo'] === "professor" && isset($_POST['id_relatorio'])) {
                invalidarRelatorio($_POST['id_relatorio']);
            } else {
                json_return(["status" => "error", "message" => "Usuário não autorizado."]);
            }
            break;
        case 'revert_validation':
            if ($_SESSION['tipo'] === "professor" && isset($_POST['id_relatorio'])) {
                reverterValidacao($_POST['id_relatorio']);
            } else {
                json_return(["status" => "error", "message" => "Usuário não autorizado."]);
            }
            break;
        default:
            json_return(["status" => "error", "message" => "Ação não encontrada."]);
            break;
    }
} elseif ($method === "GET") {
    switch ($action) {
        case 'list':
            listarAtividadesEnviadas($id_usuario);
            break;
        case 'feedback':
            if (isset($_GET['id_relatorio'])) {
                obterFeedback($_GET['id_relatorio']);
            } else {
                json_return(["status" => "error", "message" => "ID do relatório não fornecido."]);
            }
            break;
        case 'get_feedback_history':
            if (isset($_GET['id_relatorio'])) {
                obterFeedbackHistorico($_GET['id_relatorio']);
            } else {
                json_return(["status" => "error", "message" => "ID do relatório não fornecido."]);
            }
            break;
        case 'get_alteracao_history':
            if (isset($_GET['id_relatorio'])) {
                obterHistoricoAlteracoes($_GET['id_relatorio']);
            } else {
                json_return(["status" => "error", "message" => "ID do relatório não fornecido."]);
            }
            break;
        case 'get_activity':
            if (isset($_GET['id_relatorio'])) {
                obterAtividade($_GET['id_relatorio']);
            } else {
                json_return(["status" => "error", "message" => "ID do relatório não fornecido."]);
            }
            break;
        case 'get_certificate':
            if (isset($_GET['id_relatorio'])) {
                obterCertificado($_GET['id_relatorio']);
            } else {
                json_return(["status" => "error", "message" => "ID do relatório não fornecido."]);
            }
            break;
        case 'list_by_aluno':
            if ($_SESSION['tipo'] === 'aluno') {
                $categoriaId = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
                listarRelatoriosPorAluno($id_usuario, $categoriaId);
            } else {
                json_return(["status" => "error", "message" => "Inválido."]);
            }
            break;
        case 'list_by_professor':
            if ($_SESSION['tipo'] === 'professor') {
                $categoriaId = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
                listarRelatoriosPorProfessor($id_usuario, $categoriaId);
            } else {
                json_return(["status" => "error", "message" => "Inválido."]);
            }
            break;
        case 'verificar_alta_demanda':
            if ($_SESSION['tipo'] === 'professor') {
                verificarAltaDemandaValidacao($id_usuario);
            } else {
                json_return(["status" => "error", "message" => "Usuário não autorizado."]);
            }
            break;
        default:
            json_return(["status" => "error", "message" => "Ação não encontrada."]);
            break;
    }
} else {
    json_return(["status" => "error", "message" => "Método não suportado"]);
}

function adicionarAtividade($dados) {
    global $connection;
    $id_aluno = $_SESSION['id_usuario'];

    if (empty($dados['nome']) || empty($dados['data_realizacao']) || empty($dados['id_categoria']) || empty($dados['texto_reflexao']) || $dados['id_categoria'] === 'Selecionar categoria') {
        json_return(["status" => "error", "message" => "Dados incompletos."]);
        return;
    }

    $nome = mysqli_real_escape_string($connection, $dados['nome']);
    $orgao = mysqli_real_escape_string($connection, $dados['orgao']);
    $data_realizacao = mysqli_real_escape_string($connection, $dados['data_realizacao']);
    $id_categoria = mysqli_real_escape_string($connection, $dados['id_categoria']);
    $texto_reflexao = mysqli_real_escape_string($connection, $dados['texto_reflexao']);
    $data_envio = date('Y-m-d');
    $status = 'Aguardando validacao';

    // FORMATAR DD/MM/AAAA PARA AAAA-MM-DD
    $data_realizacao_formatada = date_format(date_create_from_format('d/m/Y', $data_realizacao), 'Y-m-d');

    if (isset($_FILES['certificado']) && $_FILES['certificado']['error'] === UPLOAD_ERR_OK) {
        $fileType = $_FILES['certificado']['type'];
        $fileExtension = pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION);

        if ($fileType === 'application/pdf' && strtolower($fileExtension) === 'pdf') {
            $certificado = file_get_contents($_FILES['certificado']['tmp_name']);
            $certificado = mysqli_real_escape_string($connection, $certificado);
        } else {
            json_return(["status" => "error", "message" => "Apenas arquivos PDF são permitidos."]);
            return;
        }
    } else {
        $certificado = null;
    }

    $colunas = "nome, data_realizacao, id_categoria, texto_reflexao, data_envio, status, id_aluno, certificado, orgao";
    $valores = "'$nome', '$data_realizacao_formatada', $id_categoria, '$texto_reflexao', '$data_envio', '$status', $id_aluno, '$certificado', '$orgao'";

    $resultado = inserir_dado("relatorio_atividade", $colunas, $valores);

    if ($resultado['status'] === 'success') {
        json_return(["status" => "success", "message" => "Atividade adicionada com sucesso"]);
    } else {
        json_return(["status" => "error", "message" => "Erro ao adicionar a atividade: " . $resultado['message']]);
    }
}

function listarAtividadesEnviadas($id_usuario) {
    $query = "
        SELECT 
            r.id,
            r.nome,
            r.orgao,
            c.nome AS categoria,
            r.data_realizacao,
            r.status
        FROM relatorio_atividade r
        LEFT JOIN categoria c ON r.id_categoria = c.id
        WHERE r.id_aluno = $id_usuario
        ORDER BY r.data_envio DESC
    ";

    $atividades = consultar_dado($query);

    if (is_array($atividades) && count($atividades) > 0) {
        json_return($atividades);
    } else {
        json_return(["status" => "error", "message" => "Nenhuma atividade encontrada."]);
    }
}

function listarRelatoriosPorAluno($id_usuario, $categoriaId = null) {
    $query = "
        SELECT 
            r.id,
            r.nome,
            c.nome AS categoria,
            r.data_realizacao,
            r.status
        FROM relatorio_atividade r
        LEFT JOIN categoria c ON r.id_categoria = c.id
        WHERE r.id_aluno = $id_usuario
    ";

    // SE categoriaId FOI RECEBIDO -> FILTRAR
    if ($categoriaId) {
        $query .= " AND r.id_categoria = $categoriaId";
    }

    // ORDENAR DATA DE ENVIO - MAIS RECENTE
    $query .= " ORDER BY r.data_envio DESC";

    $atividades = consultar_dado($query);

    if (is_array($atividades) && count($atividades) > 0) {
        json_return($atividades);
    } else {
        json_return(["status" => "error", "message" => "Nenhuma atividade encontrada."]);
    }
}

function listarRelatoriosPorProfessor($id_professor, $categoriaId = null) {
    $query = "
        SELECT 
            r.id, 
            r.nome AS atividade, 
            c.nome AS categoria, 
            cu.nome AS curso, 
            u.nome AS aluno, 
            r.data_realizacao, 
            r.status
        FROM relatorio_atividade r
        INNER JOIN categoria c ON r.id_categoria = c.id
        INNER JOIN curso cu ON c.id_curso = cu.id
        INNER JOIN aluno a ON r.id_aluno = a.id_usuario
        INNER JOIN usuario u ON a.id_usuario = u.id
        INNER JOIN professor_curso pc ON pc.id_curso = cu.id
        WHERE pc.id_professor = $id_professor
    ";

    // SE categoriaId FOI RECEBIDO -> FILTRAR
    if ($categoriaId) {
        $query .= " AND r.id_categoria = $categoriaId";
    }

    // ORDENAR DATA DE ENVIO - MAIS RECENTE
    $query .= " ORDER BY r.data_envio DESC";

    $result = consultar_dado($query);

    if (is_array($result) && count($result) > 0) {
        json_return($result);
    } else {
        json_return(["status" => "error", "message" => "Nenhum relatório encontrado para o professor informado."]);
    }
}

function atualizarAtividade($dados) {
    global $connection;

    $id_relatorio = mysqli_real_escape_string($connection, $dados['id_relatorio']);
    $nome = mysqli_real_escape_string($connection, $dados['nome']);
    $data_realizacao = mysqli_real_escape_string($connection, $dados['data_realizacao']);
    $id_categoria = mysqli_real_escape_string($connection, $dados['id_categoria']);
    $texto_reflexao = mysqli_real_escape_string($connection, $dados['texto_reflexao']);

    // REGISTRAR HISTORICO DO FEEDBACK ATUAL
    adicionarFeedbackHistorico($id_relatorio);

    // SE A ATIVIDADE ESTIVER EM "RECATEGORIZACAO" ATUALIZA PARA AGUARDANDO VALIDACAO
    $status = "Aguardando validacao";
    if ($dados['status'] === "Recategorizacao") {
        $status = "Aguardando validacao";
    }

    // HISTÓRICO DO RELATÓRIO
    $queryHistorico = "INSERT INTO historico_relatorio_atividade 
        (id_relatorio, nome_anterior, texto_reflexao_anterior, data_realizacao_anterior, status_anterior, certificado_anterior)
        SELECT id, nome, texto_reflexao, data_realizacao, status, certificado 
        FROM relatorio_atividade 
        WHERE id = $id_relatorio";

    if (!mysqli_query($connection, $queryHistorico)) {
        json_return(["status" => "error", "message" => "Erro ao registrar o histórico de alterações: " . mysqli_error($connection)]);
        return;
    }

    $certificado = null;
    if (isset($_FILES['certificado']) && $_FILES['certificado']['error'] === UPLOAD_ERR_OK) {
        $fileType = $_FILES['certificado']['type'];
        $fileExtension = pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION);

        if ($fileType === 'application/pdf' && strtolower($fileExtension) === 'pdf') {
            $certificado = file_get_contents($_FILES['certificado']['tmp_name']);
            $certificado = mysqli_real_escape_string($connection, $certificado);
        } else {
            json_return(["status" => "error", "message" => "Apenas arquivos PDF são permitidos."]);
            return;
        }
    }

    $atributos = "
        nome = '$nome', 
        data_realizacao = '$data_realizacao', 
        id_categoria = $id_categoria, 
        texto_reflexao = '$texto_reflexao', 
        status = '$status'
    ";

    if ($certificado !== null) {
        $atributos .= ", certificado = '$certificado'";
    }

    $condicao = "id = $id_relatorio";

    $resultado = atualizar_dado('relatorio_atividade', $atributos, $condicao);

    if ($resultado['status'] === 'success') {
        json_return(["status" => "success", "message" => "Atividade atualizada com sucesso e status alterado para 'Aguardando validação'."]);
    } else {
        json_return(["status" => "error", "message" => "Erro ao atualizar a atividade: " . $resultado['message']]);
    }
}

function obterAtividade($id_relatorio) {
    $query = "
        SELECT 
            r.id,
            r.nome,
            r.texto_reflexao,
            r.data_realizacao,
            r.data_envio,
            r.status,
            r.id_categoria
        FROM relatorio_atividade r
        WHERE r.id = $id_relatorio
    ";

    $atividade = consultar_dado($query);

    if (is_array($atividade) && count($atividade) > 0) {
        json_return($atividade[0]);
    } else {
        json_return(["status" => "error", "message" => "Atividade não encontrada."]);
    }
}

function obterHistoricoAlteracoes($id_relatorio) {
    $query = "
        SELECT nome_anterior, texto_reflexao_anterior, data_realizacao_anterior, data_alteracao
        FROM historico_relatorio_atividade
        WHERE id_relatorio = $id_relatorio
        ORDER BY data_alteracao DESC
    ";

    $historico = consultar_dado($query);

    if (is_array($historico) && count($historico) > 0) {
        json_return($historico);
    } else {
        json_return([]);
    }
}


function obterFeedback($id_relatorio) {
    $query = "
        SELECT 
            f.texto_feedback,
            r.nome AS atividade_nome
        FROM feedback f
        INNER JOIN relatorio_atividade r ON f.id_relatorio = r.id
        WHERE f.id_relatorio = $id_relatorio
    ";

    $feedback = consultar_dado($query);

    if (is_array($feedback) && count($feedback) > 0) {
        json_return($feedback[0]);
    } else {
        json_return(["status" => "error", "message" => "Feedback não encontrado."]);
    }
}

// EM DESENVOLVIMENTO - TESTAR - NÃO FINALIZADO
function obterCertificado($id_relatorio) {
    $query = "SELECT certificado FROM relatorio_atividade WHERE id = $id_relatorio";
    $result = consultar_dado($query);

    if (is_array($result) && count($result) > 0) {
        $certificado = $result[0]['certificado'];

        // Verificar se o certificado começa com a assinatura PDF ("%PDF")
        $pdfHeader = substr($certificado, 0, 4);
        if ($pdfHeader === "%PDF") {
            header("Content-Type: application/pdf");
            header("Content-Disposition: inline; filename=certificado.pdf");
            echo $certificado;
        } else {
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Este certificado está corrompido. Tente novamente mais tarde."]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "Certificado não encontrado"]);
    }
}


// ALTA DEMANDA
function verificarAltaDemandaValidacao($id_professor) {
    global $connection;

    $queryContagemPendentes = "
        SELECT COUNT(*) AS pendentes 
        FROM relatorio_atividade r
        INNER JOIN categoria c ON r.id_categoria = c.id
        INNER JOIN curso cu ON c.id_curso = cu.id
        INNER JOIN professor_curso pc ON pc.id_curso = cu.id
        WHERE pc.id_professor = $id_professor AND r.status = 'Aguardando validacao'
    ";

    $resultadoContagem = consultar_dado($queryContagemPendentes);
    $quantidadePendentes = isset($resultadoContagem[0]['pendentes']) ? $resultadoContagem[0]['pendentes'] : 0;

    json_return([ "status" => "success", "quantidadePendentes" => $quantidadePendentes ]);
}

function adicionarFeedbackHistorico($id_relatorio, $id_professor = null) {
    // FEEDBACK ATUAL
    $queryFeedback = "SELECT * FROM feedback WHERE id_relatorio = $id_relatorio";
    $feedbackAtual = consultar_dado($queryFeedback);

    if (!empty($feedbackAtual)) {
        // ADD FEEDBACK ATUAL NO HISTORICO FEEDBACK
        $id_feedback = $feedbackAtual[0]['id'];
        $texto_feedback = $feedbackAtual[0]['texto_feedback'];
        $data_envio = $feedbackAtual[0]['data_envio'];

        if ($id_professor === null) {
            $id_professor = $feedbackAtual[0]['id_professor'];
        }

        $versao = 1 + (int) consultar_dado("SELECT MAX(versao) AS max_versao FROM feedback_historico WHERE id_relatorio = $id_relatorio")[0]['max_versao'];

        $atributosHistorico = "id_feedback, id_relatorio, texto_feedback, data_envio, id_professor, versao";
        $valoresHistorico = "$id_feedback, $id_relatorio, '$texto_feedback', '$data_envio', $id_professor, $versao";

        inserir_dado("feedback_historico", $atributosHistorico, $valoresHistorico);

        // DELETAR FEEDBACK ATUAL APOS INSERCAO HISTORICO
        deletar_dado("feedback", "id = $id_feedback");
    }
}

function validarRelatorio($dados) {
    global $connection;

    $id_relatorio = mysqli_real_escape_string($connection, $dados['id_relatorio']);
    $horas_validadas = mysqli_real_escape_string($connection, $dados['horas_validadas']);

    $atributos = "status = 'Valido', horas_validadas = '$horas_validadas'";
    $condicao = "id = $id_relatorio";

    $resultado = atualizar_dado("relatorio_atividade", $atributos, $condicao);

    if ($resultado['status'] === 'success') {
        json_return(["status" => "success", "message" => "Relatório validado com sucesso."]);
    } else {
        json_return(["status" => "error", "message" => "Erro ao validar o relatório."]);
    }
}

function invalidarRelatorio($id_relatorio) {
    global $connection;
    $feedback = mysqli_real_escape_string($connection, $_POST['feedback']);
    $id_professor = $_SESSION['id_usuario'];

    $atributos = "status = 'Invalido'";
    $condicao = "id = $id_relatorio";

    $resultado = atualizar_dado("relatorio_atividade", $atributos, $condicao);
    if ($resultado['status'] === 'success') {
        // ADD FEEDBACK
        $colunas = "texto_feedback, id_relatorio, data_envio, id_professor";
        $valores = "'$feedback', $id_relatorio, NOW(), $id_professor";
        $feedbackResult = inserir_dado("feedback", $colunas, $valores);

        if ($feedbackResult['status'] === 'success') {
            // Armazenar no histórico do feedback - REVERRRRRRRRRRRRRR
            //$id_feedback = $feedbackResult['insert_id'];
            //adicionarFeedbackHistorico($id_feedback, $id_relatorio, $feedback, $id_professor);
            json_return(["status" => "success", "message" => "Relatório invalidado com sucesso."]);
        } else {
            json_return(["status" => "error", "message" => "Erro ao salvar o feedback: " . $feedbackResult['message']]);
        }
    } else {
        json_return(["status" => "error", "message" => "Erro ao invalidar o relatório."]);
    }
}

function reverterValidacao($id_relatorio) {
    global $connection;
    $justificativa = mysqli_real_escape_string($connection, $_POST['justificativa']);
    $id_professor = $_SESSION['id_usuario'];

    adicionarFeedbackHistorico($id_relatorio, $id_professor);

    $atributosReversao = "id_relatorio, justificativa, id_professor";
    $valoresReversao = "$id_relatorio, '$justificativa', $id_professor";
    inserir_dado("reversao_validacao", $atributosReversao, $valoresReversao);

    $atributosRelatorio = "status = 'Aguardando validacao'";
    $condicaoRelatorio = "id = $id_relatorio";
    atualizar_dado("relatorio_atividade", $atributosRelatorio, $condicaoRelatorio);

    json_return(["status" => "success", "message" => "Validação revertida com sucesso."]);
}

function obterFeedbackHistorico($id_relatorio) {
    $query = "
        SELECT texto_feedback, data_envio, versao
        FROM feedback_historico
        WHERE id_relatorio = $id_relatorio
        ORDER BY versao DESC
    ";

    $historico = consultar_dado($query);

    if (is_array($historico) && count($historico) > 0) {
        json_return($historico);
    } else {
        json_return([]);
    }
}
?>
