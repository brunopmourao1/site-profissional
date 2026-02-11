<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (login_admin($username, $password)) {
        header('Location: index.php');
        exit;
    }

    $error = 'Usuario ou senha invalidos.';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Dashboard</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body class="login-page">
<main class="login-card">
    <h1>Dashboard</h1>
    <p>Gerencie o conteudo e visual do seu site.</p>
    <?php if ($error !== ''): ?>
        <div class="alert"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Usuario
            <input type="text" name="username" required>
        </label>
        <label>Senha
            <input type="password" name="password" required>
        </label>
        <button type="submit">Entrar</button>
    </form>
    <small>Primeiro acesso: admin / admin123</small>
</main>
</body>
</html>
