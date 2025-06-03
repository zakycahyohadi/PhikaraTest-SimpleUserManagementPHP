<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}

$db = new DB();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Prevent deleting yourself
    if ($id != $_SESSION['user_id']) {
        $db->query("DELETE FROM tbl_user WHERE id = $id");
    }
}

$db->close();
header('Location: dashboard.php');
exit;
?>