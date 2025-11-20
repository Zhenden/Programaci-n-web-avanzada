<link rel="stylesheet" href="../assets/css/styles.css">

<nav>
  <ul>
    <li><a href="index.php">Home</a></li>
    <?php if(SessionManager::isAuthenticated()): ?>
      <li><a href="index.php?action=dishes">Platos</a></li>
      <li><a href="index.php?action=orders">Pedidos</a></li>
      <li><a href="index.php?action=comments/user">Mis Comentarios</a></li>
      <?php if(SessionManager::get('role_name') == 'Administrator'): ?>
        <li><a href="index.php?action=comments">Administrar Comentarios</a></li>
      <?php endif; ?>
      <li class="nav-user-info">
        <span>Usuario: <?= htmlspecialchars(SessionManager::get('username', '')) ?> (<?= htmlspecialchars(SessionManager::get('role_name', '')) ?>)</span>
      </li>
      <li><a href="index.php?action=logout">Salir</a></li>
    <?php else: ?>
      <li><a href="index.php?action=login">Login</a></li>
      <li><a href="index.php?action=register">Registro</a></li>
    <?php endif; ?>
  </ul>
</nav>