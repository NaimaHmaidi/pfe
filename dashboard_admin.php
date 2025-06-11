<?php
ob_start();
session_start();
require_once '../config.php';

// Récupération de la page demandée (pour les includes dynamiques)
$page = isset($_GET['page']) ? $_GET['page'] : 'liste';

//vérifie si  token de connexion est toujours valide.
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE remember_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
         $_SESSION['role'] = $user['role'];  // Important pour vérifier le rôle ensuite
    }
}

// Vérifier si l'utilisateur est connecté et a le rôle 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Récupérer le nom de l'admin depuis la base de données
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user  = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $admin_name = $user['username']; // Nom de l'admin
} else {
    $admin_name = "Utilisateur inconnu"; // Si l'utilisateur n'est pas trouvé dans la base de données
}

// Affichage du message de succès
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Effacer le message après l'affichage
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
     <!--  le lien CDN pour Lineicons -->
    <link href="https://cdn.jsdelivr.net/npm/lineicons@3.0.0/dist/lineicons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


    <style>
        body {
            background-color: #dcdee0;
            font-family: 'Arial', sans-serif;
            margin: 0;
        }
        .container-fluid {
            display: flex;
            height: 100vh;
            width: 100%;
        }
        .sidebar {
            position: fixed; /* Fixe la sidebar */
            top: 0;
            left: 0;
            width: 264px; /* Largeur de la sidebar */
            background: var(--bs-dark);
            color: #fff;
            height: 100%;
            padding-top: 20px;
            z-index: 1000; /* S'assurer que la sidebar se superpose au contenu */
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 15px 20px;
            display: block;
            font-weight: bold;
            border-radius: 5px;
        }
        .sidebar h4 {
            color: #adb5bd;  
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #adb5bd;
            color: #fff;
        }
        .content {
            position: relative;
            margin-left: 264px; /* Marge pour éviter le chevauchement avec la sidebar */
            padding: 20px;
            flex: 1;
            background-color: #dcdee0;
            overflow-y: auto;
        }

        .header {
            color: black;
            padding: 5px;
            margin-bottom: 20px;
            text-align: right; /* Aligner le texte à droite */
            width: 100%;
            font-size: 16px;
            font-weight: bold; /* Texte en gras */
        }

        .logo-container {
            text-align: center; /* Centrer le logo */
            margin-bottom: 40px; /* Ajouter un espacement sous le logo */
        }

        .logo {
            width: 50%; /* Ajuste la largeur du logo selon ton besoin */
            max-width: 200px; /* Taille maximale du logo */
            height: auto; /* Garde les proportions du logo */
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo-container">
                <img src="images/logo.webp" alt="Logo" class="logo">
            </div>
            <a href="dashboard_admin.php?page=dashboard" class="active"><i class="fas fa-users"></i> Tableau de bord</a>
            <a href="dashboard_admin.php?page=users" ><i class="fas fa-plus"></i> Utilisateurs</a>
                    <a href="dashboard-admin.php?page=add-user" class="d-none"><i class="fas fa-cogs"></i> Ajouter un utilisateur</a><!-- class="d-none" masquer ajouter  mais reste fonctionnel -->
                    <a href="dashboard-admin.php?page=update-user" class="d-none"><i class="fas fa-cogs"></i> Modifier un utilisateur</a><!-- class="d-none" masquer modifier  mais reste fonctionnel -->
            <a href="../projet/index.php" ><i class="fas fa-cogs"></i> Projets</a>  
            <a href="../tache/tache.php"><i class="fas fa-chalkboard-teacher"></i> Taches</a>
            <a href="dashboard_admin.php?page=add-class"><i class="fas fa-plus-square"></i> Paramètres</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="header">
                <p>Bienvenue, <?= htmlspecialchars($admin_name) ?> !</p>
            </div>
            <div class="container mt-3">
                <?php
                // Inclure les pages en fonction du paramètre 'page'
                if ($page == 'dashboard') {
                    include 'dashboard.php'; // Afficher la liste des étudiants
                } elseif ($page == 'users') {
                    include 'liste-user.php'; // Afficher le formulaire d'ajout d'un étudiant
                } elseif ($page == 'add-user') {
                    include 'add-user.php'; // Afficher le formulaire de modification d'un étudiant
                } elseif ($page == 'update-user') {
                    include 'update-user.php';  // Gérer les classes
                } else {
                    echo '<p>Page non trouvée</p>';
                }
                ?>
            
            </div>
        </div>
    </div>
</body>
</html>