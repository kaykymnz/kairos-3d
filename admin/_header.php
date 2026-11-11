<?php

$pageTitle = $pageTitle ?? 'Administração';
$successMessage = $successMessage ?? '';
$errorMessage = $errorMessage ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= escape_html($pageTitle) ?> · Kairos 3D</title>
  <link rel="icon" type="image/png" href="../assets/logo-primaria.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
  <header class="admin-header">
    <a class="admin-brand" href="index.php"><img src="../assets/logo-primaria.png" alt="Kairos 3D"><span>Administração</span></a>
    <?php if (!empty($_SESSION['admin_user'])): ?>
      <form method="post" action="logout.php">
        <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
        <button class="button button-quiet" type="submit">Sair</button>
      </form>
    <?php endif; ?>
  </header>
  <main class="admin-main">
    <h1><?= escape_html($pageTitle) ?></h1>
    <?php if ($successMessage !== ''): ?><p class="notice notice-success"><?= escape_html($successMessage) ?></p><?php endif; ?>
    <?php if ($errorMessage !== ''): ?><p class="notice notice-error"><?= escape_html($errorMessage) ?></p><?php endif; ?>
