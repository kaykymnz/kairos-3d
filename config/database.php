<?php

declare(strict_types=1);

$localConfigPath = __DIR__ . '/local.php';
$localConfig = is_file($localConfigPath) ? require $localConfigPath : [];
$databaseHost = getenv('KAIROS_DB_HOST') ?: ($localConfig['host'] ?? 'localhost');
$databaseName = getenv('KAIROS_DB_NAME') ?: ($localConfig['name'] ?? 'kairos_3d');
$databaseUser = getenv('KAIROS_DB_USER') ?: ($localConfig['user'] ?? 'root');
$databasePassword = getenv('KAIROS_DB_PASSWORD') ?: ($localConfig['password'] ?? '');
$databaseDsn = getenv('KAIROS_DB_DSN') ?: sprintf(
    'mysql:host=%s;dbname=%s;charset=utf8mb4',
    $databaseHost,
    $databaseName
);

return new PDO($databaseDsn, $databaseUser, $databasePassword, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
