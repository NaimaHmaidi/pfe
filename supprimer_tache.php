<?php
require_once '../config.php';

if ($_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['projet_id'])) {
    $id = (int)$_GET['id'];
    $projet_id = (int)$_GET['projet_id'];

    $stmt = $pdo->prepare("DELETE FROM taches WHERE id = ?");
    $stmt->execute([$id]);

     header("Location: liste_tache.php?projet_id=" . $projet_id);
    exit;
}
?>
