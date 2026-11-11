<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
    session_start();
}

const PRODUCT_CATEGORIES = ['Geek', 'Decor', 'Setup'];
const PRODUCT_BADGES = ['QUERIDINHO', 'NOVO DROP', 'LOOT DA SEMANA'];
const PRODUCT_IMAGE_COLUMNS = ['imagem', 'imagem_2', 'imagem_3', 'imagem_4'];

function database(): PDO
{
    static $connection;

    if (!$connection instanceof PDO) {
        $connection = require __DIR__ . '/../config/database.php';
    }

    return $connection;
}

function escape_html(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function format_price(string|float|int $value): string
{
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
}

function product_colors(string|array|null $value): array
{
    if (is_string($value)) {
        $value = json_decode($value, true);
    }

    if (!is_array($value)) {
        return [];
    }

    return array_values(array_filter(
        $value,
        static fn ($color): bool => is_string($color) && preg_match('/^#[0-9a-fA-F]{6}$/', $color)
    ));
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('A sessão expirou. Atualize a página e tente novamente.');
    }
}

function require_admin(): void
{
    if (empty($_SESSION['admin_user'])) {
        header('Location: login.php');
        exit;
    }
}

function redirect_to(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function uploaded_product_image(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Não foi possível enviar a imagem. Verifique o limite de upload do servidor.');
    }

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('O arquivo enviado não é válido.');
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('Cada imagem deve ter no máximo 5 MB.');
    }

    $imageInfo = getimagesize($file['tmp_name']);
    $mime = $imageInfo['mime'] ?? '';
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Formato não permitido. Use JPG, PNG ou WEBP.');
    }

    $relativeDirectory = 'uploads/produtos';
    $directory = dirname(__DIR__) . DIRECTORY_SEPARATOR . $relativeDirectory;

    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('Não foi possível preparar a pasta de imagens.');
    }

    $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $extensions[$mime];
    $destination = $directory . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Não foi possível salvar a imagem enviada.');
    }

    return $relativeDirectory . '/' . $filename;
}

function remove_product_image_if_unused(string $path, ?int $excludeProductId = null): void
{
    if (!preg_match('#^(assets|uploads/produtos)/[a-zA-Z0-9._-]+$#', $path)) {
        return;
    }

    $conditions = [];
    $parameters = [];
    foreach (PRODUCT_IMAGE_COLUMNS as $column) {
        $conditions[] = "`$column` = ?";
        $parameters[] = $path;
    }

    $sql = 'SELECT COUNT(*) FROM produtos WHERE (' . implode(' OR ', $conditions) . ')';
    if ($excludeProductId !== null) {
        $sql .= ' AND id <> ?';
        $parameters[] = $excludeProductId;
    }

    $statement = database()->prepare($sql);
    $statement->execute($parameters);

    if ((int) $statement->fetchColumn() > 0) {
        return;
    }

    $absolutePath = dirname(__DIR__) . DIRECTORY_SEPARATOR
        . str_replace('/', DIRECTORY_SEPARATOR, $path);

    if (is_file($absolutePath) && !unlink($absolutePath)) {
        throw new RuntimeException('O registro foi removido, mas não foi possível apagar a imagem ' . $path . '.');
    }
}

function product_images(array $product): array
{
    return array_values(array_filter(
        array_map(
            static fn (string $column): ?string => $product[$column] ?? null,
            PRODUCT_IMAGE_COLUMNS
        ),
        static fn ($path): bool => is_string($path) && $path !== ''
    ));
}
