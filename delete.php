
<?php
include '../config.php';
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
//la suppression a traves l'id
$id =$_GET['id']; 
//la req supp 
$stmt = $pdo->prepare(query: 'DELETE FROM users WHERE id = ? ');
//l'execution de la requete
$stmt->execute(params: [$id]);
header(header: 'Location: dashboard_admin.php?page=users');
exit();

?>