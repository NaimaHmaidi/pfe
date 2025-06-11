<?php
// dashboard.php
include '../config.php';

// 1. Nombre total d'utilisateurs
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// 2. Utilisateurs par rôle
$usersByRole = $pdo->query("SELECT role, COUNT(*) AS total FROM users GROUP BY role")->fetchAll(PDO::FETCH_ASSOC);

// 3. Utilisateurs récents (exemple : les 5 derniers inscrits)
$recentUsers = $pdo->query("SELECT username, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4">Statistiques des Utilisateurs</h2>

<div class="row g-4 mb-5">

  <!-- Card Total Utilisateurs -->
  <div class="col-md-4">
    <div class="card shadow-sm border-0 rounded-4 text-white bg-secondary  p-4">
      <div class="d-flex align-items-center">
        <div class="me-3">
          <i class="fas fa-users fa-3x"></i>
        </div>
        <div>
          <h5 class="mb-1">Total des utilisateurs</h5>
          <h2 class="fw-bold"><?= $totalUsers ?></h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Cards par rôle -->
  <?php foreach ($usersByRole as $roleStat): 
    $percent = ($totalUsers > 0) ? round(($roleStat['total'] / $totalUsers) * 100) : 0;
  ?>
  <div class="col-md-4">
    <div class="card shadow-sm border-0 rounded-4 p-4">
      <h5 class="mb-3 text-capitalize"><?= htmlspecialchars($roleStat['role']) ?></h5>
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-bold fs-4"><?= $roleStat['total'] ?></span>
        <span><?= $percent ?>%</span>
      </div>
      <div class="progress" style="height: 10px;">
        <div class="progress-bar bg-info" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

</div>

<!-- Derniers utilisateurs -->
<h4 class="mb-3">5 Derniers utilisateurs inscrits</h4>
<table class="table table-hover rounded-4 shadow-sm">
  <thead class="table-light">
    <tr>
      <th>Nom d'utilisateur</th>
      <th>Date d'inscription</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($recentUsers as $user): ?>
    <tr>
      <td><?= htmlspecialchars($user['username']) ?></td>
      <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<style>
  .card {
    transition: transform 0.3s ease;
  }
  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgb(0 0 0 / 0.15);
  }
  table tr:hover {
    background-color: #f1f5f9;
  }
</style>