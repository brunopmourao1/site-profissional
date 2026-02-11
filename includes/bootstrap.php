<?php
// Shared bootstrap for public site and admin dashboard.
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define('ROOT_PATH', dirname(__DIR__));
define('DATA_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'site.json');
define('UPLOADS_DIR', ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads');
define('UPLOADS_URL', 'uploads');
