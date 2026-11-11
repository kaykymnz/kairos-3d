<?php

require __DIR__ . '/../includes/functions.php';
require_admin();
require __DIR__ . '/_product_handler.php';

$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    try {
        save_product_from_form($_POST, $_FILES);
        redirect_to('index.php?salvo=1');
    } catch (InvalidArgumentException|RuntimeException $error) {
        $errorMessage = $error->getMessage();
    }
}

$product = null;
$pageTitle = 'Cadastrar produto';
require __DIR__ . '/_header.php';
require __DIR__ . '/_product_form.php';
require __DIR__ . '/_footer.php';
