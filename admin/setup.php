<?php

require __DIR__ . '/../includes/functions.php';

$userCount = (int) database()->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
if ($userCount > 0) {
    redirect_to('login.php');
}

$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $username = trim((string) ($_POST['usuario'] ?? ''));
    $password = (string) ($_POST['senha'] ?? '');
    $confirmation = (string) ($_POST['confirmar_senha'] ?? '');

    if ($username === '' || mb_strlen($username) > 80) {
        $errorMessage = 'Informe um usuário com até 80 caracteres.';
    } elseif (strlen($password) < 10) {
        $errorMessage = 'A senha deve ter pelo menos 10 caracteres.';
    } elseif ($password !== $confirmation) {
        $errorMessage = 'A confirmação da senha não confere.';
    } else {
        $statement = database()->prepare('INSERT INTO usuarios (usuario, senha) VALUES (?, ?)');
        $statement->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        session_regenerate_id(true);
        $_SESSION['admin_user'] = $username;
        redirect_to('index.php');
    }
}

$pageTitle = 'Criar usuário administrador';
require __DIR__ . '/_header.php';
?>
<section class="admin-panel auth-panel">
  <p>A criação inicial fica disponível somente enquanto ainda não houver uma conta administrativa.</p>
  <form method="post" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
    <label>Usuário
      <input name="usuario" maxlength="80" autocomplete="username" required value="<?= escape_html($_POST['usuario'] ?? '') ?>">
    </label>
    <label>Senha
      <input type="password" name="senha" minlength="10" autocomplete="new-password" required>
    </label>
    <label>Confirmar senha
      <input type="password" name="confirmar_senha" minlength="10" autocomplete="new-password" required>
    </label>
    <button class="button button-primary" type="submit">Criar acesso</button>
  </form>
</section>
<?php require __DIR__ . '/_footer.php'; ?>
