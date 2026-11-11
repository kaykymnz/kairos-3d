<?php

require __DIR__ . '/../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}
verify_csrf_token();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$active = filter_input(INPUT_POST, 'ativo', FILTER_VALIDATE_INT);
if (!$id || !in_array($active, [0, 1], true)) {
    http_response_code(400);
    exit('Dados inválidos.');
}

$statement = database()->prepare(
    'UPDATE produtos SET ativo = ?, destaque = CASE WHEN ? = 0 THEN 0 ELSE destaque END WHERE id = ?'
);
$statement->execute([$active, $active, $id]);
redirect_to('index.php');
