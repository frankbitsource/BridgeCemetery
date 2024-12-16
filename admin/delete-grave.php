<?php
require_once 'auth.php';
require_once '../includes/config.php';
require_once '../includes/db.php';
checkAdmin();

if (isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM graves WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        header("Location: dashboard.php");
        exit;
    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>
