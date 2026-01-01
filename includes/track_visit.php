<?php
// Script pour enregistrer les visites
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

$page_url = $_SERVER['REQUEST_URI'];
$ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

$stmt = $conn->prepare("INSERT INTO site_statistics (date_visite, page_url, ip_address, user_agent, user_id) VALUES (CURRENT_DATE(), ?, ?, ?, ?)");
$stmt->bind_param("sssi", $page_url, $ip_address, $user_agent, $user_id);
$stmt->execute();
$stmt->close();
$conn->close();
?>

