<?php
declare(strict_types=1);

require_once __DIR__ . '/site_data.php';

function is_logged_in(): bool
{
    return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function login_admin(string $username, string $password): bool
{
    $data = load_site_data();
    $admin = $data['admin'] ?? [];

    $storedUsername = (string)($admin['username'] ?? 'admin');
    $passwordHash = (string)($admin['password_hash'] ?? '');
    $passwordPlain = (string)($admin['password_plain'] ?? '');

    if ($username !== $storedUsername) {
        return false;
    }

    $ok = false;
    if ($passwordHash !== '') {
        $ok = password_verify($password, $passwordHash);
    } elseif ($passwordPlain !== '') {
        $ok = hash_equals($passwordPlain, $password);
        if ($ok) {
            $data['admin']['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            $data['admin']['password_plain'] = '';
            save_site_data($data);
        }
    }

    if ($ok) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = $storedUsername;
    }

    return $ok;
}

function logout_admin(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
