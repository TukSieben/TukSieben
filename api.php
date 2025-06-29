<?php
// CORS-Header setzen
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight-Request (OPTIONS) beantworten
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// Speicherort für Usernamen
$file = __DIR__ . '/twitch_users.txt';

// Username speichern per GET (z.B. /api.php?username=foo)
if (isset($_GET['username'])) {
    $username = trim($_GET['username']);
    if ($username !== '') {
        file_put_contents($file, $username . "\n", FILE_APPEND | LOCK_EX);
        http_response_code(200);
        echo 'OK';
        exit;
    } else {
        http_response_code(400);
        echo 'No username';
        exit;
    }
}

// GET: Usernamen abrufen (ohne Datei zu leeren)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['username'])) {
    if (file_exists($file)) {
        $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        header('Content-Type: application/json');
        echo json_encode($users);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }
}

// Fallback
http_response_code(405);
echo 'Method Not Allowed';
exit;
