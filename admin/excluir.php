<?php

require __DIR__ . '/../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}
verify_csrf_token();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Produto inválido.');
}

$connection = database();
$connection->beginTransaction();
$statement = $connection->prepare('SELECT * FROM produtos WHERE id = ? FOR UPDATE');
$statement->execute([$id]);
$product = $statement->fetch();

if (!$product) {
    $connection->rollBack();
    http_response_code(404);
    exit('Produto não encontrado.');
}
if ((int) $product['ativo'] === 1) {
    $connection->rollBack();
    http_response_code(409);
    exit('Inative o produto antes de excluí-lo permanentemente.');
}

$images = product_images($product);
$delete = $connection->prepare('DELETE FROM produtos WHERE id = ?');
$delete->execute([$id]);
$connection->commit();

foreach (array_unique($images) as $image) {
    remove_product_image_if_unused($image);
}

redirect_to('index.php');
