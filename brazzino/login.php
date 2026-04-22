<?php
// ============================================================
//  login.php — Brazzino Casino
// ============================================================
require_once 'config.php';

$page_title = 'Ingresar · Brazzino Casino';
$extra_css   = '<link rel="stylesheet" href="css/brazzino-inner.css">';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Ingresa tu correo y contraseña.';
    } else {
        try {
            $db   = pdo();
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            $valid = false;
            if ($user) {
                // Soporta SHA2 (datos demo) y password_hash
                if (strlen($user['password']) === 64) {
                    $valid = (hash('sha256', $password) === $user['password']);
                } else {
                    $valid = password_verify($password, $user['password']);
                }
            }

            if ($valid) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombre']     = $user['nombre'];
                $_SESSION['email']      = $user['email'];
                $_SESSION['es_admin']   = (bool)$user['es_admin'];
                $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect"); exit;
            } else {
                $error = 'Correo o contraseña incorrectos.';
            }
        } catch (Exception $e) {
            $error = 'Error de conexión: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

         <div class="brz-section">
            <div class="brz-login-wrap">
               <div class="brz-login-card">

                  <div class="brz-login-logo">
                     <span class="brz-diamond">♦</span>
                     <div class="brz-title" style="font-size:28px;line-height:36px;margin-top:8px">
                        BRAZZINO CASINO
                     </div>
                     <p style="color:#666;font-size:12px;letter-spacing:3px;margin-top:4px">
                        ACCESO A TU CUENTA
                     </p>
                  </div>

                  <?php if ($error): ?>
                  <div class="brz-alert brz-alert-error"><?= htmlspecialchars($error) ?></div>
                  <?php endif; ?>

                  <form method="POST">
                     <div class="brz-form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" placeholder="tu@correo.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                     </div>
                     <div class="brz-form-group" style="margin-bottom:28px">
                        <label>Contraseña</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                     </div>
                     <button type="submit" class="brz-btn" style="width:100%;font-size:16px;padding:13px">
                        <i class="fa fa-sign-in"></i>&nbsp; Ingresar
                     </button>
                  </form>

                  <p style="text-align:center;margin-top:20px;font-size:13px;color:#666">
                     ¿No tienes cuenta?
                     <a href="register.php" style="color:#bc883d;font-weight:600">Regístrate aquí</a>
                  </p>

                  <!-- Credenciales demo -->
                  <div style="margin-top:22px;padding:14px;background:rgba(188,136,61,.07);
                              border:1px solid rgba(188,136,61,.15);border-radius:6px;
                              font-size:12px;color:#888;text-align:center">
                     <strong style="color:#bc883d">Demo:</strong><br>
                     admin@casinoglamour.mx / Admin2026!<br>
                     jugador@casinoglamour.mx / Player2026!
                  </div>
               </div>
            </div>
         </div>

<?php include 'includes/footer.php'; ?>
