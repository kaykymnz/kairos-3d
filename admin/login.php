<?php

require __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['admin_user'])) {
    redirect_to('index.php');
}

$userCount = (int) database()->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
if ($userCount === 0) {
    redirect_to('setup.php');
}

$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $username = trim((string) ($_POST['usuario'] ?? ''));
    $password = (string) ($_POST['senha'] ?? '');
    $statement = database()->prepare('SELECT usuario, senha FROM usuarios WHERE usuario = ?');
    $statement->execute([$username]);
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['senha'])) {
        session_regenerate_id(true);
        $_SESSION['admin_user'] = $user['usuario'];
        redirect_to('index.php');
    }

    $errorMessage = 'Usuário ou senha inválidos.';
}

$pageTitle = 'Entrar no painel';
require __DIR__ . '/_header.php';
?>
<section class="admin-panel auth-panel">
  <form method="post" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
    <label>Usuário
      <input name="usuario" maxlength="80" autocomplete="username" required value="<?= escape_html($_POST['usuario'] ?? '') ?>">
    </label>
    <label>Senha
      <input type="password" name="senha" autocomplete="current-password" required>
    </label>
    <button class="button button-primary" type="submit">Entrar</button>
  </form>
</section>
<?php require __DIR__ . '/_footer.php'; ?>
