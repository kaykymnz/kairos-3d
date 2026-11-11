<?php

require __DIR__ . '/../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}
verify_csrf_token();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$featured = filter_input(INPUT_POST, 'destaque', FILTER_VALIDATE_INT);
if (!$id || !in_array($featured, [0, 1], true)) {
    http_response_code(400);
    exit('Dados inválidos.');
}

$statement = database()->prepare('UPDATE produtos SET destaque = ? WHERE id = ? AND ativo = 1');
$statement->execute([$featured, $id]);
redirect_to('index.php');
