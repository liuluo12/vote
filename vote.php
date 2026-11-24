<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-store');

$file  = __DIR__ . '/votes.json';
$names = json_decode(file_get_contents(__DIR__ . '/names.json'), true);

$fp = fopen($file, 'c+');
if (!$fp) {
    http_response_code(500);
    echo json_encode(array('ok' => false, 'msg' => 'storage'));
    exit;
}
flock($fp, LOCK_EX);
$raw   = stream_get_contents($fp);
$votes = $raw ? json_decode($raw, true) : array();
if (!is_array($votes)) {
    $votes = array();
}

if (isset($_GET['status'])) {
    flock($fp, LOCK_UN);
    fclose($fp);
    $out = array('total' => count($votes), 'votes' => $votes);
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

$name  = isset($_GET['name'])  ? trim($_GET['name']) : '';
$token = isset($_GET['token']) ? substr(trim($_GET['token']), 0, 80) : '';

if ($token === '' || !in_array($name, $names, true)) {
    flock($fp, LOCK_UN);
    fclose($fp);
    echo json_encode(array('ok' => false, 'msg' => 'invalid'), JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($votes[$token])) {
    $done = $votes[$token];
    flock($fp, LOCK_UN);
    fclose($fp);
    echo json_encode(array('ok' => false, 'reason' => 'voted', 'name' => $done, 'total' => count($votes)), JSON_UNESCAPED_UNICODE);
    exit;
}

$votes[$token] = $name;
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($votes, JSON_UNESCAPED_UNICODE));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(array('ok' => true, 'name' => $name, 'total' => count($votes)), JSON_UNESCAPED_UNICODE);
