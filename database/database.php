<?php
$connection = mysqli_connect("localhost", "root", "", "taskcheck");

if(!$connection) {
    die("Erro ao conectar db");
}
?>