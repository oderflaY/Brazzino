<?php
// ============================================================
//  index.php — Brazzino Casino
//  DISEÑO: cs.zip original (sin modificar CSS ni clases)
//  DATOS:  Paquetes de créditos cargados desde casino_db
// ============================================================
require_once 'config.php';

$page_title = 'Brazzino Casino — Compra tus Créditos';

// Cargar paquetes desde BD
try {
    $db       = pdo();
    $stmt     = $db->query("SELECT * FROM productos WHERE activo = 1 ORDER BY precio ASC LIMIT 6");
    $paquetes = $stmt->fetchAll();
} catch (Exception $e) {
    $paquetes  = [];
    $db_error  = $e->getMessage();
}

// Imágenes del casino (cs.zip) asignadas a cada paquete
$game_imgs = [
    'images/game1.jpg',
    'images/game2.jpg',
    'images/game3.jpg',
    'images/game4.jpg',
    'images/game5.jpg',
    'images/game6.jpg',
];

$msg = $_SESSION['cart_msg'] ?? null;
unset($_SESSION['cart_msg']);

include 'includes/header.php';
?>

         <!-- ===== BANNER — Fiel al cs.zip original ===== -->
         <section class="slider_section" style="display:block;width:100%;overflow:hidden;position:relative">
            <div class="banner_main" style="position:relative;margin-bottom:-1px">
               <img src="images/bg_main.jpg" alt="Brazzino Casino"
                    style="width:100%;display:block;max-height:100vh;object-fit:cover">
               <div class="container-fluid padding3"
                    style="position:absolute;bottom:0;left:0;right:0;padding:0">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="text-bg" style="position:static;margin-top:0;padding-bottom:30px">
                           <a href="#creditos"
                              style="display:inline-block;margin:0 auto">Jugar Ahora</a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </section>

         <!-- ===== NOTIFICACIÓN CARRITO ===== -->
         <?php if ($msg): ?>
         <div id="brz-msg" style="position:fixed;top:20px;right:20px;z-index:9999;
              background:<?= $msg['tipo']==='ok' ? 'linear-gradient(135deg,#bc883d,#f5e47b)' : '#8B0000' ?>;
              color:#000;padding:14px 24px;border-radius:8px;font-weight:600;
              box-shadow:0 4px 20px rgba(0,0,0,.5);font-size:15px;max-width:320px">
            <?= htmlspecialchars($msg['texto']) ?>
         </div>
         <script>setTimeout(()=>document.getElementById('brz-msg')?.remove(),3500)</script>
         <?php endif; ?>

         <?php if (isset($db_error)): ?>
         <div style="background:#8B0000;color:#fff;padding:16px 30px;text-align:center;font-size:14px">
            ⚠ Error BD: <?= htmlspecialchars($db_error) ?> — Verifica config.php
         </div>
         <?php endif; ?>

         <!-- ===== SECCIÓN COMPRAR CRÉDITOS (id="creditos") ===== -->
         <!-- Usa las clases CSS ORIGINALES del casino: .casino .game_box .game .titlepage -->
         <div id="creditos" class="casino">
            <div class="container">
               <div class="row">
                  <div class="col-md-12">
                     <div class="titlepage">
                        <h2>Comprar Créditos</h2>
                        <span></span>
                     </div>
                  </div>
               </div>

               <?php if (empty($paquetes)): ?>
               <div class="row">
                  <div class="col-md-12" style="text-align:center;padding:60px 0;color:#f5e47b;font-size:18px">
                     No hay paquetes disponibles. Verifica la base de datos.
                  </div>
               </div>
               <?php else: ?>
               <div class="row">

               <?php foreach ($paquetes as $i => $pack):
                  // Columna y margen según posición (replica la estructura original del cs.zip)
                  $col_class = ($i < 3) ? 'col-md-4 padding_bottom' : 'col-md-4 margin_bottom1';
                  if ($i === 5) $col_class = 'col-md-4';
               ?>
                  <div class="<?= $col_class ?>">
                     <!-- .game_box: clase original cs.zip — borde dorado, padding -->
                     <div class="game_box">
                        <figure>
                           <img src="<?= $game_imgs[$i] ?>"
                                alt="<?= htmlspecialchars($pack['nombre_paquete']) ?>"
                                style="width:100%;display:block">
                        </figure>
                     </div>

                     <!-- .game: clase original cs.zip — tarjeta blanca superpuesta -->
                     <div class="game" style="max-width:240px;padding:12px 10px 10px">
                        <!-- Nombre del paquete -->
                        <h3 style="font-size:15px;font-weight:700;text-align:center;
                                   margin-bottom:4px;color:#1b0905;line-height:1.3">
                           <?= htmlspecialchars($pack['nombre_paquete']) ?>
                        </h3>
                        <!-- Precio en estilo dorado -->
                        <p style="text-align:center;font-weight:700;font-size:18px;
                                  background-image:linear-gradient(to bottom,#bc883d,#f5e47b,#bc883d);
                                  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
                                  margin:4px 0 10px">
                           $<?= number_format($pack['precio'], 2) ?> <small style="font-size:11px">MXN</small>
                        </p>

                        <!-- Botón Agregar al Carrito — usa el estilo .read_more del cs.zip -->
                        <form method="POST" action="apis/agregar_carrito.php">
                           <input type="hidden" name="id_producto" value="<?= $pack['id'] ?>">
                           <input type="hidden" name="cantidad"    value="1">
                           <button type="submit" class="read_more"
                                   style="width:100%;text-align:center;float:none;display:block;
                                          margin:0 auto;border:none;cursor:pointer;font-size:13px;
                                          padding:9px 0">
                              <i class="fa fa-cart-plus"></i> Agregar
                           </button>
                        </form>
                     </div>
                  </div>
               <?php endforeach; ?>

               </div><!-- end .row paquetes -->
               <?php endif; ?>

               <!-- Botón Ver Carrito -->
               <div class="row" style="padding-top:40px;padding-bottom:60px">
                  <div class="col-md-12" style="text-align:center">
                     <a href="carrito.php" class="banner_main .text-bg"
                        style="display:inline-block;
                               background-image:linear-gradient(to bottom,#bc883d,#f5e47b,#bc883d);
                               color:#000;padding:12px 50px;font-size:16px;font-weight:700;
                               text-transform:uppercase;border-radius:30px;
                               box-shadow:0 4px 18px rgba(0,0,0,.4);letter-spacing:2px">
                        <i class="fa fa-shopping-cart"></i>&nbsp; Ver mi Carrito
                     </a>
                  </div>
               </div>

            </div><!-- end .container -->
         </div>
         <!-- ===== FIN COMPRAR CRÉDITOS ===== -->

         <!-- ===== SECCIÓN LICENCIAS (cs.zip original) ===== -->
         <div class="licens">
            <div class="container">
               <div class="row">
                  <div class="col-md-12">
                     <div class="titlepage">
                        <h2>Plataforma Confiable</h2>
                        <span></span>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <div id="licens" class="carousel slide licens_Carousel" data-ride="carousel">
                        <ol class="carousel-indicators">
                           <li data-target="#licens" data-slide-to="0" class="active"></li>
                           <li data-target="#licens" data-slide-to="1"></li>
                           <li data-target="#licens" data-slide-to="2"></li>
                        </ol>
                        <div class="carousel-inner">
                           <?php
                           $slides = [
                              ['titulo' => 'Seguridad Total', 'texto' => 'Brazzino Casino opera bajo los más altos estándares de seguridad electrónica. Tus datos y créditos siempre protegidos con encriptación de nivel bancario.'],
                              ['titulo' => 'Pago Instantáneo', 'texto' => 'Compra tus paquetes de créditos en segundos. Aceptamos múltiples métodos de pago. Tu factura se genera automáticamente al confirmar.'],
                              ['titulo' => 'Soporte 24/7',    'texto' => 'Nuestro equipo está disponible las 24 horas, los 7 días de la semana. Escríbenos y te atenderemos de inmediato.'],
                           ];
                           foreach ($slides as $idx => $slide): ?>
                           <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                              <div class="container">
                                 <div class="carousel-caption">
                                    <div class="row d_flex">
                                       <div class="col-md-6">
                                          <div class="test_box">
                                             <div class="jons">
                                                <h4><?= $slide['titulo'] ?></h4>
                                             </div>
                                             <p><?= $slide['texto'] ?></p>
                                             <a class="read_more" href="#creditos">Comprar Ahora</a>
                                          </div>
                                       </div>
                                       <div class="col-md-6">
                                          <div class="test_box">
                                             <div class="jons">
                                                <figure><img src="images/jons_img1.png" alt="Brazzino"></figure>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <?php endforeach; ?>
                        </div>
                        <a class="carousel-control-prev" href="#licens" role="button" data-slide="prev">
                           <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
                        </a>
                        <a class="carousel-control-next" href="#licens" role="button" data-slide="next">
                           <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                        </a>
                     </div>
                     <p class="lorem">Brazzino Casino es una plataforma de entretenimiento electrónico 100% segura. Compra tus créditos, disfruta la experiencia y recibe tu factura automáticamente con IVA desglosado y folio oficial.</p>
                  </div>

                  <div id="about" class="col-md-12">
                     <ul class="spinit">
                        <li><a href="#creditos">Comprar Créditos</a></li>
                        <li><a href="carrito.php">Mi Carrito</a></li>
                        <li><a href="#creditos">+6 Paquetes</a></li>
                        <li><a href="login.php">Mi Cuenta</a></li>
                     </ul>
                     <div class="two_box">
                        <div class="row d_flex">
                           <div class="col-md-4">
                              <div class="many_box_img">
                                 <figure><img src="images/imag.jpg" alt="Brazzino Casino"></figure>
                              </div>
                           </div>
                           <div class="col-md-8">
                              <div class="many_box">
                                 <p>Brazzino Casino es la experiencia definitiva de entretenimiento electrónico. Elige tu paquete de créditos, agrégalo al carrito y confirma tu pago en segundos. Tu factura con IVA 16% se genera de forma completamente automática con folio correlativo oficial (FAC-YYYY-NNN).</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- ===== TESTIMONIALES (cs.zip original) ===== -->
         <div id="customer" class="customer">
            <div class="container">
               <div class="row">
                  <div class="col-md-12">
                     <div class="titlepage">
                        <h2>Lo que Dicen Nuestros Jugadores</h2>
                        <span></span>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-10 offset-md-1">
                     <div class="customer_text">
                        <p>"Brazzino Casino es increíble. Compré mi paquete Platino en minutos y la factura llegó al instante. La experiencia de juego es de primer nivel. ¡Totalmente recomendado para todos los que buscan entretenimiento electrónico de calidad!"</p>
                        <div class="customer_box">
                           <i><img src="images/customer.png" alt="Jugador Brazzino"></i>
                           <h4>Carlos M.</h4>
                           <span>Jugador Diamante</span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- ===== CONTACTO (cs.zip original) ===== -->
         <div id="contact" class="reqeste">
            <div class="container">
               <div class="row">
                  <div class="col-md-12">
                     <div class="titlepage">
                        <h2>Contáctanos</h2>
                        <span></span>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6 offset-md-3">
                     <form id="cochang" class="form_main" onsubmit="return false">
                        <div class="row">
                           <div class="col-md-12">
                              <input class="form_control" placeholder="Nombre" type="text">
                           </div>
                           <div class="col-md-12">
                              <input class="form_control" placeholder="Teléfono" type="tel">
                           </div>
                           <div class="col-md-12">
                              <input class="form_control" placeholder="Correo" type="email">
                           </div>
                           <div class="col-md-12">
                              <input class="form_control" placeholder="Mensaje" type="text">
                           </div>
                           <div class="col-md-12">
                              <button class="send_btn" type="button">Enviar</button>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>

<?php include 'includes/footer.php'; ?>
