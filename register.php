<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];// Récupération du rôle
    $errors = [];

      // Vérification des champs
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }

   // Vérification si le nom existe déjà
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->rowCount() > 0) {
                $errors[] = "Le nom d'utilisateur est déjà pris";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur checking: " . $e->getMessage();
        }
    }

    // Si pas d'erreur, insérer un utilisateur
     if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hacher le mot de passe
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $role]);

            // Redirection après inscription
            header ('Location: login.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = "Erreur creating account: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            width: 100%;
            padding: 15px;
            background-color: #4128a7;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color:rgb(43, 110, 123);
        } 

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .footer a {
            color: #4800ff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .alert {
            font-size: 0.9rem;
        }

    </style>
</head>

<body>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <strong>Inscription </strong>
            </div>
            <div class="card-body">
                <!-- Affichage des erreurs si elles existent -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire d'inscription -->
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur :</label>
                        <input type="text" name="username" class="form-control" id="username" placeholder="saisir votre nom" value="<?= htmlspecialchars($username ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe :</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="saisir votre mot de passe" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe :</label>
                        <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirmer votre mot de passe" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle :</label>
                        <select name="role" class="form-select" id="role" required>
                            <option value="admin">Administrateur</option>
                            <option value="chef">Chef de projet</option>
                            <option value="membre">Membre</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <button class="btn btn-lg btn-light w-100 fs-6"><img src="images/google.png" style="width:20px" class="me-2">S'inscrire avec Google</button>
                    </div>

                    

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">S'inscrire</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="footer">
            <p>Déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>