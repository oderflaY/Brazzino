<?php
require_once 'config.php';
$page_title = 'Registro · Brazzino Casino';
$extra_css   = '<link rel="stylesheet" href="css/brazzino-inner.css">';
$error = null; $success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (!$nombre || !$email || !$password) $error = 'Todos los campos son obligatorios.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Correo inválido.';
    elseif (strlen($password) < 6) $error = 'Contraseña mínimo 6 caracteres.';
    elseif ($password !== $confirm) $error = 'Las contraseñas no coinciden.';
    else {
        try {
            $db = pdo();
            $chk = $db->prepare("SELECT id FROM usuarios WHERE email=?");
            $chk->execute([$email]);
            if ($chk->fetch()) $error = 'Ya existe una cuenta con ese correo.';
            else {
                $db->prepare("INSERT INTO usuarios(nombre,email,password) VALUES(?,?,?)")
                   ->execute([$nombre,$email,password_hash($password,PASSWORD_DEFAULT)]);
                $success = true;
            }
        } catch(Exception $e) { $error = 'Error: '.$e->getMessage(); }
    }
}

include 'includes/header.php';
?>
         <div class="brz-section">
            <div class="brz-login-wrap">
               <div class="brz-login-card">
                  <div class="brz-login-logo">
                     <span class="brz-diamond">♦</span>
                     <div class="brz-title" style="font-size:24px;line-height:32px;margin-top:8px">CREAR CUENTA</div>
                     <p style="color:#666;font-size:12px;letter-spacing:3px">BRAZZINO CASINO</p>
                  </div>

                  <?php if ($success): ?>
                     <div class="brz-alert brz-alert-success">
                        ✓ Cuenta creada. <a href="login.php" style="color:#bc883d">Inicia sesión aquí</a>
                     </div>
                  <?php elseif ($error): ?>
                     <div class="brz-alert brz-alert-error"><?= htmlspecialchars($error) ?></div>
                  <?php endif; ?>

                  <?php if (!$success): ?>
                  <form method="POST">
                     <div class="brz-form-group">
                        <label>Nombre completo</label>
                        <input type="text" name="nombre" placeholder="Tu nombre"
                               value="<?= htmlspecialchars($_POST['nombre']??'') ?>" required>
                     </div>
                     <div class="brz-form-group">
                        <label>Correo</label>
                        <input type="email" name="email" placeholder="tu@correo.com"
                               value="<?= htmlspecialchars($_POST['email']??'') ?>" required>
                     </div>
                     <div class="brz-form-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" placeholder="Mín. 6 caracteres" required>
                     </div>
                     <div class="brz-form-group" style="margin-bottom:28px">
                        <label>Confirmar Contraseña</label>
                        <input type="password" name="confirm" placeholder="Repite tu contraseña" required>
                     </div>
                     <button type="submit" class="brz-btn" style="width:100%;font-size:16px;padding:13px">
                        <i class="fa fa-user-plus"></i>&nbsp; Crear Cuenta
                     </button>
                  </form>
                  <?php endif; ?>

                  <p style="text-align:center;margin-top:18px;font-size:13px;color:#666">
                     ¿Ya tienes cuenta? <a href="login.php" style="color:#bc883d;font-weight:600">Inicia sesión</a>
                  </p>
               </div>
            </div>
         </div>
<?php include 'includes/footer.php'; ?>
