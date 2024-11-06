<?php
include 'database.php';

function json_return($data) {
    echo json_encode($data);
    exit;
}

// CRUD - DADOS DB
// CREATE
function inserir_dado($tabela, $colunas, $valores) {
    global $connection;

    $query = "INSERT INTO $tabela ($colunas) VALUES ($valores)";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        return ["status" => "error", "message" => "Erro ao inserir dado: " . mysqli_error($connection)];
    } else {
        return ["status" => "success", "insert_id" => mysqli_insert_id($connection)];
    }
}

// READ/RETRIEVE
function consultar_dado($query) {
    global $connection;

    $result = mysqli_query($connection, $query);
    if (!$result) {
        die("Erro na consulta: " . mysqli_error($connection));
    }

    $dados = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $dados[] = $row;
        }
    }

    return $dados;
}

// UPDATE
function atualizar_dado($tabela, $atributos, $condicao) {
    global $connection;

    $query = "UPDATE $tabela SET $atributos WHERE $condicao";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        return ["status" => "error", "message" => "Erro ao atualizar dado: " . mysqli_error($connection)];
    } else {
        return ["status" => "success", "affected_rows" => mysqli_affected_rows($connection)];
    }
}

// DELETE
function deletar_dado($tabela, $condicao) {
    global $connection;

    $query = "DELETE FROM $tabela WHERE $condicao";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        return ["status" => "error", "message" => "Erro ao excluir dado: " . mysqli_error($connection)];
    } else {
        return ["status" => "success", "message" => "Dado excluído com sucesso."];
    }
}
?>
