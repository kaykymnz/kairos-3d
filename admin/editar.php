<?php

require __DIR__ . '/../includes/functions.php';
require_admin();
require __DIR__ . '/_product_handler.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Produto inválido.');
}

$statement = database()->prepare('SELECT * FROM produtos WHERE id = ?');
$statement->execute([$id]);
$product = $statement->fetch();
if (!$product) {
    http_response_code(404);
    exit('Produto não encontrado.');
}

$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    try {
        save_product_from_form($_POST, $_FILES, $product);
        redirect_to('index.php?salvo=1');
    } catch (InvalidArgumentException|RuntimeException $error) {
        $errorMessage = $error->getMessage();
    }
    $product = array_merge($product, $_POST);
    foreach (PRODUCT_IMAGE_COLUMNS as $column) {
        $product[$column] = $product[$column] ?? null;
    }
}

$pageTitle = 'Editar produto';
require __DIR__ . '/_header.php';
require __DIR__ . '/_product_form.php';
require __DIR__ . '/_footer.php';
