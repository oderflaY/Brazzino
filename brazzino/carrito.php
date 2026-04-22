<?php
// ============================================================
//  carrito.php — Brazzino Casino · Mi Carrito
// ============================================================
require_once 'config.php';

$page_title = 'Mi Carrito · Brazzino Casino';
$extra_css   = '<link rel="stylesheet" href="css/brazzino-inner.css">';

// Acciones
$action = $_POST['action'] ?? null;
$pid    = (int)($_POST['id_producto'] ?? 0);

if ($action === 'remove' && $pid > 0) {
    unset($_SESSION['carrito'][$pid]);
} elseif ($action === 'update' && $pid > 0) {
    $qty = max(1, (int)($_POST['cantidad'] ?? 1));
    $_SESSION['carrito'][$pid]['cantidad'] = $qty;
} elseif ($action === 'clear') {
    $_SESSION['carrito'] = [];
}

$carrito = $_SESSION['carrito'] ?? [];

$total = 0;
foreach ($carrito as $it) $total += $it['precio'] * $it['cantidad'];

include 'includes/header.php';
?>

         <!-- === CARRITO === -->
         <div class="brz-section">
            <div class="container">
               <div class="row">
                  <div class="col-md-12">
                     <div class="brz-title">Mi Carrito</div>
                     <div class="brz-title-line"></div>
                  </div>
               </div>

               <?php if (empty($carrito)): ?>
               <div class="row">
                  <div class="col-md-12" style="text-align:center;padding:60px 0">
                     <i class="fa fa-shopping-cart" style="font-size:56px;color:#3a1a0a;display:block;margin-bottom:18px"></i>
                     <p style="color:#888;font-size:17px;margin-bottom:24px">Tu carrito está vacío.</p>
                     <a href="index.php#creditos" class="brz-btn">
                        <i class="fa fa-diamond"></i>&nbsp; Ver Paquetes
                     </a>
                  </div>
               </div>

               <?php else: ?>
               <div class="row">
                  <!-- TABLA -->
                  <div class="col-md-8">
                     <div class="brz-card">
                        <div class="brz-card-title">♦ Paquetes Seleccionados</div>
                        <table class="brz-table">
                           <thead>
                              <tr>
                                 <th>Paquete</th>
                                 <th>Precio</th>
                                 <th>Cantidad</th>
                                 <th>Subtotal</th>
                                 <th></th>
                              </tr>
                           </thead>
                           <tbody>
                           <?php foreach ($carrito as $item): ?>
                              <tr>
                                 <td style="color:#f0e8d0;font-weight:600">
                                    <?= htmlspecialchars($item['nombre_paquete']) ?>
                                 </td>
                                 <td class="brz-price">$<?= number_format($item['precio'], 2) ?></td>
                                 <td>
                                    <form method="POST" style="display:inline">
                                       <input type="hidden" name="action" value="update">
                                       <input type="hidden" name="id_producto" value="<?= $item['id'] ?>">
                                       <div class="brz-qty">
                                          <button type="submit" name="cantidad"
                                                  value="<?= max(1,$item['cantidad']-1) ?>"
                                                  class="brz-qty-btn">−</button>
                                          <span class="brz-qty-val"><?= $item['cantidad'] ?></span>
                                          <button type="submit" name="cantidad"
                                                  value="<?= $item['cantidad']+1 ?>"
                                                  class="brz-qty-btn">+</button>
                                       </div>
                                    </form>
                                 </td>
                                 <td class="brz-price">$<?= number_format($item['precio']*$item['cantidad'], 2) ?></td>
                                 <td>
                                    <form method="POST" style="display:inline">
                                       <input type="hidden" name="action" value="remove">
                                       <input type="hidden" name="id_producto" value="<?= $item['id'] ?>">
                                       <button type="submit" class="brz-btn-danger">
                                          <i class="fa fa-trash"></i>
                                       </button>
                                    </form>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                           </tbody>
                        </table>

                        <div style="margin-top:20px;display:flex;gap:14px;flex-wrap:wrap">
                           <a href="index.php#creditos" class="brz-btn-outline">
                              <i class="fa fa-arrow-left"></i>&nbsp; Seguir Comprando
                           </a>
                           <form method="POST" style="display:inline">
                              <input type="hidden" name="action" value="clear">
                              <button type="submit" class="brz-btn-outline">
                                 <i class="fa fa-trash"></i>&nbsp; Vaciar Carrito
                              </button>
                           </form>
                        </div>
                     </div>
                  </div>

                  <!-- RESUMEN -->
                  <div class="col-md-4">
                     <div class="brz-card">
                        <div class="brz-card-title">♠ Resumen</div>

                        <div class="brz-summary-row">
                           <span>Subtotal (sin IVA)</span>
                           <span>$<?= number_format($total/1.16, 2) ?> MXN</span>
                        </div>
                        <div class="brz-summary-row">
                           <span>IVA 16%</span>
                           <span>$<?= number_format($total-($total/1.16), 2) ?> MXN</span>
                        </div>
                        <div class="brz-summary-row grand">
                           <span>Total</span>
                           <span>$<?= number_format($total, 2) ?> MXN</span>
                        </div>

                        <div style="margin-top:26px">
                           <a href="checkout.php" class="brz-btn" style="width:100%;display:block;margin-bottom:12px">
                              <i class="fa fa-credit-card"></i>&nbsp; Proceder al Pago
                           </a>
                        </div>

                        <div style="margin-top:14px;padding:12px;background:rgba(188,136,61,.07);
                                    border-radius:6px;font-size:12px;color:#888;text-align:center">
                           <i class="fa fa-file-text" style="color:#bc883d"></i>
                           Tu factura con IVA se genera automáticamente al pagar
                        </div>
                     </div>
                  </div>
               </div>
               <?php endif; ?>

            </div>
         </div>

<?php include 'includes/footer.php'; ?>
