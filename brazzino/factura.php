<?php
// ============================================================
//  factura.php — Brazzino Casino · Factura Automatizada
// ============================================================
require_once 'config.php';

$page_title = 'Factura · Brazzino Casino';
$extra_css   = '<link rel="stylesheet" href="css/brazzino-inner.css">';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

try {
    $db = pdo();
    $stmt = $db->prepare("
        SELECT f.id AS factura_id, f.folio_unico, f.subtotal, f.iva_16,
               f.total_final, f.fecha_emision, f.concepto,
               v.id AS venta_id, v.fecha AS fecha_venta,
               u.nombre AS nombre_usuario, u.email
        FROM facturas f
        INNER JOIN ventas   v ON v.id = f.venta_id
        INNER JOIN usuarios u ON u.id = v.id_usuario
        WHERE f.id = ? LIMIT 1");
    $stmt->execute([$id]);
    $factura = $stmt->fetch();
    if (!$factura) { header('Location: index.php'); exit; }

    $stmtD = $db->prepare("
        SELECT d.cantidad, d.precio_unitario, d.subtotal, p.nombre_paquete
        FROM venta_detalle d
        INNER JOIN productos p ON p.id = d.id_producto
        WHERE d.id_venta = ? ORDER BY d.id");
    $stmtD->execute([$factura['venta_id']]);
    $detalles = $stmtD->fetchAll();

} catch (Exception $e) {
    die('<p style="color:red;padding:40px">Error: ' . htmlspecialchars($e->getMessage()) . '</p>');
}

include 'includes/header.php';
?>

         <div class="brz-section">
            <div class="container">
               <div class="row">
                  <div class="col-md-12">
                     <div class="brz-title">Factura Generada</div>
                     <div class="brz-title-line"></div>
                  </div>
               </div>

               <!-- Botones -->
               <div class="row" style="margin-bottom:24px">
                  <div class="col-md-12" style="display:flex;gap:14px;justify-content:flex-end">
                     <button onclick="window.print()" class="brz-btn">
                        <i class="fa fa-print"></i>&nbsp; Imprimir
                     </button>
                     <a href="index.php#creditos" class="brz-btn-outline">
                        <i class="fa fa-diamond"></i>&nbsp; Seguir Comprando
                     </a>
                  </div>
               </div>

               <!-- DOCUMENTO FACTURA -->
               <div class="row">
                  <div class="col-md-12">
                     <div class="brz-factura-doc" id="facturaDoc">

                        <!-- Encabezado dorado -->
                        <div class="brz-factura-header">
                           <div>
                              <div class="fh-brand">♦ BRAZZINO CASINO</div>
                              <div class="fh-sub">PLATAFORMA DE ENTRETENIMIENTO ELECTRÓNICO</div>
                              <div style="font-size:11px;color:#5a3300;margin-top:6px">
                                 RFC: BZC-2024-001 &nbsp;·&nbsp; Durango, Dgo., México
                              </div>
                           </div>
                           <div class="fh-folio">
                              <div style="font-size:11px;color:#5a3300;letter-spacing:2px">FOLIO</div>
                              <div class="fh-folio-num"><?= htmlspecialchars($factura['folio_unico']) ?></div>
                              <div style="font-size:12px;color:#5a3300">
                                 <?= date('d/m/Y H:i', strtotime($factura['fecha_emision'])) ?>
                              </div>
                              <span style="background:#1b0905;color:#f5e47b;font-size:11px;
                                           padding:3px 12px;border-radius:10px;font-weight:700;
                                           letter-spacing:1px;margin-top:6px;display:inline-block">
                                 ✓ PAGADA
                              </span>
                           </div>
                        </div>

                        <!-- Cuerpo -->
                        <div class="brz-factura-body">

                           <!-- Datos cliente y venta -->
                           <div class="row" style="margin-bottom:20px">
                              <div class="col-md-6">
                                 <div style="font-size:11px;color:rgba(188,136,61,.7);letter-spacing:2px;
                                             text-transform:uppercase;margin-bottom:6px">Cliente</div>
                                 <div style="font-size:16px;font-weight:700;color:#f0e8d0">
                                    <?= htmlspecialchars($factura['nombre_usuario']) ?>
                                 </div>
                                 <div style="font-size:13px;color:#888">
                                    <?= htmlspecialchars($factura['email']) ?>
                                 </div>
                              </div>
                              <div class="col-md-6" style="text-align:right">
                                 <div style="font-size:11px;color:rgba(188,136,61,.7);letter-spacing:2px;
                                             text-transform:uppercase;margin-bottom:6px">Referencia</div>
                                 <div style="color:#f0e8d0;font-weight:600">
                                    Venta #<?= str_pad($factura['venta_id'],6,'0',STR_PAD_LEFT) ?>
                                 </div>
                                 <div style="font-size:12px;color:#888">
                                    <?= date('d/m/Y H:i', strtotime($factura['fecha_venta'])) ?>
                                 </div>
                              </div>
                           </div>

                           <!-- Concepto fijo -->
                           <div style="background:rgba(188,136,61,.07);border:1px solid rgba(188,136,61,.15);
                                       border-radius:6px;padding:12px 16px;margin-bottom:22px;font-size:13px">
                              <span style="color:rgba(188,136,61,.7);font-size:11px;
                                           letter-spacing:2px;text-transform:uppercase">Concepto: </span>
                              <span style="color:#f0e8d0"><?= htmlspecialchars($factura['concepto']) ?></span>
                           </div>

                           <!-- Tabla conceptos -->
                           <table class="brz-fact-table">
                              <thead>
                                 <tr>
                                    <th>Descripción</th>
                                    <th style="text-align:center">Cant.</th>
                                    <th style="text-align:right">P. Unitario</th>
                                    <th style="text-align:right">Importe</th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php foreach ($detalles as $det): ?>
                                 <tr>
                                    <td><?= htmlspecialchars($det['nombre_paquete']) ?></td>
                                    <td style="text-align:center"><?= $det['cantidad'] ?></td>
                                    <td style="text-align:right;color:#bbb">
                                       $<?= number_format($det['precio_unitario'],2) ?>
                                    </td>
                                    <td style="text-align:right" class="brz-price">
                                       $<?= number_format($det['subtotal'],2) ?>
                                    </td>
                                 </tr>
                              <?php endforeach; ?>
                              </tbody>
                           </table>

                           <!-- Totales -->
                           <div style="max-width:300px;margin-left:auto;margin-top:10px">
                              <div class="brz-summary-row">
                                 <span>Subtotal</span>
                                 <span>$<?= number_format($factura['subtotal'],2) ?> MXN</span>
                              </div>
                              <div class="brz-summary-row">
                                 <span>IVA 16%</span>
                                 <span>$<?= number_format($factura['iva_16'],2) ?> MXN</span>
                              </div>
                              <div class="brz-summary-row grand">
                                 <span>Total</span>
                                 <span>$<?= number_format($factura['total_final'],2) ?> MXN</span>
                              </div>
                           </div>
                        </div>

                        <!-- Pie de factura -->
                        <div class="brz-factura-footer">
                           <div>
                              Brazzino Casino · Folio: <strong><?= htmlspecialchars($factura['folio_unico']) ?></strong>
                              · Factura #<?= $factura['factura_id'] ?>
                           </div>
                           <div>♦</div>
                        </div>

                     </div><!-- end .brz-factura-doc -->
                  </div>
               </div>

            </div>
         </div>

<?php include 'includes/footer.php'; ?>

<style>
@media print {
   .sidebar,.head_top,#sidebar,.overlay,.loader_bg,
   .brz-btn,.brz-btn-outline,footer,.footer { display:none !important; }
   #content { margin:0 !important; }
   .brz-section { background:#fff !important; padding:0 !important; }
   .brz-factura-doc { border:1px solid #ccc !important; box-shadow:none !important; }
   .brz-factura-body, .brz-fact-table td, .brz-summary-row { color:#111 !important; }
   .brz-factura-header { background: #bc883d !important; }
   .brz-title, .brz-price, .brz-summary-row.grand { -webkit-text-fill-color: #333 !important; }
}
</style>
