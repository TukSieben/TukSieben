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

// POST: Username speichern oder part
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bot join
    if (isset($_POST['username'])) {
        $username = trim($_POST['username']);
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
    // Bot part
    if (isset($_POST['part'])) {
        $partUser = trim($_POST['part']);
        if ($partUser !== '' && file_exists($file)) {
            $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $users = array_filter($users, function($u) use ($partUser) {
                return strtolower($u) !== strtolower($partUser);
            });
            file_put_contents($file, implode("\n", $users) . (count($users) ? "\n" : ''));
            http_response_code(200);
            echo 'PART OK';
            exit;
        } else {
            http_response_code(400);
            echo 'No part username';
            exit;
        }
    }
}

// GET: Usernamen abrufen und Datei leeren
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($file)) {
        $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        file_put_contents($file, ''); // Datei leeren
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
