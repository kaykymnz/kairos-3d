<?php

require __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

verify_csrf_token();
$_SESSION = [];
session_destroy();
redirect_to('login.php');
