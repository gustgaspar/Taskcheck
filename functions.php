<?php
function urlFind() {
    return $_SERVER['REQUEST_URI'];
}
function urlIs($valor) {
    return $_SERVER['REQUEST_URI'] == $valor;
}

function isAbort() {
    if (http_response_code() === 404) {
        return true;
    } else {
        return false;
    }
}
