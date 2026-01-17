<?php
require_once 'config/config.php';
$details = "SITE_URL: " . SITE_URL . "\n";
file_put_contents('debug_output.txt', $details);
echo "Debug done.";
?>
