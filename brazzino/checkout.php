<?php
// ============================================================
//  checkout.php — Brazzino Casino · Pago + Factura Automática
// ============================================================
require_once 'config.php';

$page_title = 'Confirmar Pago · Brazzino Casino';
$extra_css   = '<link rel="stylesheet" href="css/brazzino-inner.css">';

if (empty($_SESSION['usuario_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php'); exit;
}

$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) { header('Location: carrito.php'); exit; }

// Totales
$total_con_iva    = 0;
foreach ($carrito as $it) $total_con_iva += $it['precio'] * $it['cantidad'];
$subtotal_sin_iva = round($total_con_iva / 1.16, 2);
$iva              = round($total_con_iva - $subtotal_sin_iva, 2);
$total_final      = round($total_con_iva, 2);

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_titular = trim($_POST['nombre_titular'] ?? '');
    $email_factura  = trim($_POST['email_factura']  ?? '');
    $num_tarjeta    = preg_replace('/\D/', '', $_POST['num_tarjeta'] ?? '');
    $cvv            = preg_replace('/\D/', '', $_POST['cvv'] ?? '');
    $exp            = trim($_POST['exp'] ?? '');

    if (!$nombre_titular || !$email_factura || strlen($num_tarjeta) < 13 || strlen($cvv) < 3 || !$exp) {
        $error = 'Por favor completa todos los campos del formulario.';
    } elseif (!filter_var($email_factura, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } else {
        try {
            $db = pdo();
            $db->beginTransaction();

            // 1) Venta
            $db->prepare("INSERT INTO ventas (id_usuario, total, estatus) VALUES (?,?,'PAGADA')")
               ->execute([$_SESSION['usuario_id'], $total_final]);
            $id_venta = (int)$db->lastInsertId();

            // 2) Detalles
            $stmtD = $db->prepare("INSERT INTO venta_detalle
               (id_venta,id_producto,cantidad,precio_unitario,subtotal) VALUES(?,?,?,?,?)");
            foreach ($carrito as $it) {
                $stmtD->execute([$id_venta,$it['id'],$it['cantidad'],
                                 $it['precio'],round($it['precio']*$it['cantidad'],2)]);
            }

            // 3) Folio automático
            $folio = generar_folio($db);

            // 4) Factura automática
            $db->prepare("INSERT INTO facturas
               (venta_id,folio_unico,subtotal,iva_16,total_final,fecha_emision,concepto)
               VALUES(?,?,?,?,?,NOW(),'Compra de créditos electrónicos para plataforma de entretenimiento')")
               ->execute([$id_venta,$folio,$subtotal_sin_iva,$iva,$total_final]);
            $factura_id = (int)$db->lastInsertId();

            $db->commit();
            $_SESSION['carrito'] = [];
            header("Location: factura.php?id=$factura_id"); exit;

        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) $db->rollBack();
            $error = 'Error al procesar el pago: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

         <div class="brz-section">
            <div class="container">
               <div class="row">
                  <div class="col-md-12">
                     <div class="brz-title">Confirmar Pago</div>
                     <div class="brz-title-line"></div>
                  </div>
               </div>

               <?php if ($error): ?>
               <div class="row" style="margin-bottom:20px">
                  <div class="col-md-12">
                     <div class="brz-alert brz-alert-error">
                        <i class="fa fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                     </div>
                  </div>
               </div>
               <?php endif; ?>

               <div class="row">
                  <!-- FORMULARIO -->
                  <div class="col-md-7">
                     <div class="brz-card">
                        <div class="brz-card-title">♣ Datos de Pago</div>
                        <form method="POST">
                           <div class="brz-form-group">
                              <label>Nombre del titular</label>
                              <input type="text" name="nombre_titular"
                                     placeholder="Como aparece en la tarjeta"
                                     value="<?= htmlspecialchars($_POST['nombre_titular']??'') ?>" required>
                           </div>
                           <div class="brz-form-group">
                              <label>Correo para factura</label>
                              <input type="email" name="email_factura"
                                     placeholder="tu@correo.com"
                                     value="<?= htmlspecialchars($_POST['email_factura'] ?? $_SESSION['email'] ?? '') ?>" required>
                           </div>
                           <div class="brz-form-group">
                              <label>Número de tarjeta</label>
                              <input type="text" name="num_tarjeta" maxlength="19"
                                     placeholder="4242 4242 4242 4242"
                                     oninput="this.value=this.value.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim()" required>
                           </div>
                           <div class="brz-form-row">
                              <div class="brz-form-group">
                                 <label>Vencimiento</label>
                                 <input type="text" name="exp" placeholder="MM/AA" maxlength="5"
                                        oninput="this.value=this.value.replace(/[^0-9\/]/g,'')" required>
                              </div>
                              <div class="brz-form-group">
                                 <label>CVV</label>
                                 <input type="text" name="cvv" placeholder="123" maxlength="4"
                                        oninput="this.value=this.value.replace(/\D/g,'')" required>
                              </div>
                           </div>

                           <!-- Aviso factura automática -->
                           <div style="background:rgba(188,136,61,.08);border:1px solid rgba(188,136,61,.2);
                                       border-radius:6px;padding:14px 18px;font-size:13px;color:#ccc;
                                       margin-bottom:22px">
                              <i class="fa fa-file-text-o" style="color:#bc883d"></i>
                              La factura con <strong style="color:#f5e47b">IVA 16% desglosado</strong> y
                              <strong style="color:#f5e47b">folio FAC-<?= date('Y') ?>-NNN</strong>
                              se genera <strong>automáticamente</strong> al confirmar. Cero pasos adicionales.
                           </div>

                           <button type="submit" class="brz-btn" style="width:100%;font-size:17px;padding:14px">
                              <i class="fa fa-lock"></i>&nbsp;
                              Pagar $<?= number_format($total_final, 2) ?> MXN
                           </button>
                        </form>
                     </div>
                  </div>

                  <!-- RESUMEN DEL PEDIDO -->
                  <div class="col-md-5">
                     <div class="brz-card" style="margin-bottom:20px">
                        <div class="brz-card-title">♦ Tu Pedido</div>
                        <?php foreach ($carrito as $it): ?>
                        <div style="display:flex;justify-content:space-between;padding:10px 0;
                                    border-bottom:1px solid rgba(255,255,255,.05);font-size:14px">
                           <div>
                              <div style="color:#f0e8d0;font-weight:600">
                                 <?= htmlspecialchars($it['nombre_paquete']) ?>
                              </div>
                              <div style="color:#666;font-size:12px">Cant: <?= $it['cantidad'] ?></div>
                           </div>
                           <span class="brz-price">$<?= number_format($it['precio']*$it['cantidad'],2) ?></span>
                        </div>
                        <?php endforeach; ?>
                     </div>

                     <div class="brz-card">
                        <div class="brz-card-title">♠ Desglose IVA</div>
                        <div class="brz-summary-row">
                           <span>Subtotal (base)</span>
                           <span>$<?= number_format($subtotal_sin_iva,2) ?> MXN</span>
                        </div>
                        <div class="brz-summary-row">
                           <span>IVA 16%</span>
                           <span>$<?= number_format($iva,2) ?> MXN</span>
                        </div>
                        <div class="brz-summary-row grand">
                           <span>Total</span>
                           <span>$<?= number_format($total_final,2) ?> MXN</span>
                        </div>
                        <p style="font-size:11px;color:#555;text-align:center;margin-top:16px">
                           <i class="fa fa-shield"></i> Pago simulado — Demo
                        </p>
                     </div>
                  </div>
               </div>
            </div>
         </div>

<?php include 'includes/footer.php'; ?>
