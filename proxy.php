<?php
if (!isset($_GET['id'])) {
  http_response_code(400);
  echo "Missing file ID";
  exit;
}

$id = $_GET['id'];
$url = "https://drive.google.com/uc?export=download&id=" . urlencode($id);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$data = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
header("Content-Type: application/json");
echo $data;
