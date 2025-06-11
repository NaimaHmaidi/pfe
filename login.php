<?php
session_start();
include 'config.php';

// Si l'utilisateur est déjà connecté, le rediriger selon son rôle
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: /gestion_tache/users/dashboard_admin.php');
            exit();
        case 'chef':
            header('Location: /gestion_tache/projet/dashboard_chef.php');
            exit();
        case 'membre':
            header('Location: /gestion_tache/tache/dashboard_membre.php');
            exit();
    }
}

// Connexion automatique via cookie
if (isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->execute([$token]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        session_regenerate_id(true); // Sécurité

        switch ($user['role']) {
            case 'admin':
                header('Location: /gestion_tache/users/dashboard_admin.php');
                exit();
            case 'chef':
                header('Location: /gestion_tache/projet/dashboard_chef.php');
                exit();
            case 'membre':
                header('Location: /gestion_tache/tache/dashboard_membre.php');
                exit();
            default:
                header('Location: login.php');
                exit();
        }
    }
}

// Traitement du formulaire de connexion
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            session_regenerate_id(true);

            if ($remember_me) {
                $token = bin2hex(random_bytes(16));
                setcookie("remember_me", $token, time() + (86400 * 30), "/");
                $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt->execute([$token, $user['id']]);
            }

            switch ($user['role']) {
                case 'admin':
                    header('Location: /gestion_tache/users/dashboard_admin.php');
                    exit();
                case 'chef':
                    header('Location: /gestion_tache/projet/dashboard_chef.php');
                    exit();
                case 'membre':
                    header('Location: /gestion_tache/tache/dashboard_membre.php');
                    exit();
                default:
                    $errors[] = "Rôle inconnu.";
            }
        } else {
            $_SESSION['error_message'] = 'Mot de passe incorrect';
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'Utilisateur non trouvé';
        header('Location: login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #dcdee0;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 100px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #4128a7;
            color: #fff;
            text-align: center;
            font-size: 1.5rem;
            padding: 1.5rem;
        }
        .btn-primary {
            width: 100%;
            padding: 15px;
            background-color: #4128a7;
            border: none;
            border-radius: 10px;
        }
        .btn-primary:hover {
            background-color: rgb(43, 110, 123);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header">
            <strong>Connexion</strong>
        </div><br>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe :</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3 d-flex justify-content-between">
                    <div class="form-check me-3">
                        <input type="checkbox" class="form-check-input" id="formChek" name="remember_me">
                        <label for="formChek" class="form-check-label text-secondary"><small>Se souvenir de moi</small></label>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer text-center mt-3">
        <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
    </div>
</div>
</body>
</html>
