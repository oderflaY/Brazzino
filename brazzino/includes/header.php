<?php
// includes/header.php — Brazzino Casino · Header compartido
// CSS/Sidebar: idéntico al cs.zip original. SIN modificar clases ni colores.
if (session_status() === PHP_SESSION_NONE) session_start();

$cart_count = 0;
if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $it) $cart_count += $it['cantidad'];
}
$current = basename($_SERVER['PHP_SELF']);
$prefix  = (strpos($_SERVER['PHP_SELF'], '/includes') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title><?= htmlspecialchars($page_title ?? 'Brazzino Casino') ?></title>
   <!-- CSS ORIGINAL cs.zip — SIN MODIFICAR -->
   <link rel="stylesheet" href="<?= $prefix ?>css/bootstrap.min.css">
   <link rel="stylesheet" href="<?= $prefix ?>css/style.css">
   <link rel="stylesheet" href="<?= $prefix ?>css/responsive.css">
   <link rel="stylesheet" href="<?= $prefix ?>css/jquery.mCustomScrollbar.min.css">
   <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
   <!-- CSS extra exclusivo de páginas internas (carrito, checkout, factura) -->
   <?php if (isset($extra_css)) echo $extra_css; ?>
   <!-- FIX DE LAYOUT: evita que el nav#sidebar (fixed) cree espacio en blanco -->
   <style>
      /* El .wrapper no debe ser flex ni crear columnas */
      .wrapper { display: block !important; }

      /* #content ocupa siempre el 100% del ancho disponible */
      #content { width: 100% !important; float: none !important; }

      /* El nav#sidebar es position:fixed → no ocupa espacio en el flujo */
      #sidebar {
         width: 280px;
         position: fixed;
         top: 0;
         left: -280px;
         height: 100vh;
         z-index: 1000;
         background: #fff;
         color: #fff;
         transition: all 0.3s;
         overflow-y: scroll;
         box-shadow: 3px 3px 3px rgba(0,0,0,.2);
      }
      #sidebar.active { left: 0; }

      /* El banner principal siempre full-width */
      .slider_section, .banner_main { width: 100% !important; }
      .banner_main img { width: 100%; display: block; }

      /* Header transparente encima del banner */
      .header {
         background: transparent;
         padding: 20px 0;
         position: absolute;
         z-index: 99;
         width: 100%;
      }

      /* Botón "Jugar Ahora" bien posicionado */
      .text-bg {
         position: absolute;
         bottom: 30px;
         left: 0; right: 0;
         text-align: center;
      }
      .text-bg a {
         display: inline-block;
         background: #fcfcfc;
         padding: 11px 48px;
         color: #000;
         font-size: 17px;
         font-weight: 700;
         text-transform: uppercase;
         letter-spacing: 2px;
         transition: all .4s ease-in;
      }
      .text-bg a:hover {
         background-image: linear-gradient(to bottom, #bc883d, #f5e47b, #bc883d);
         border-radius: 20px;
      }
   </style>
</head>
<body class="main-layout">

   <!-- loader -->
   <div class="loader_bg">
      <div class="loader"><img src="<?= $prefix ?>images/loading.gif" alt="Cargando..."></div>
   </div>

   <div class="wrapper">

      <!-- ===== SIDEBAR (cs.zip original, menú actualizado Brazzino) ===== -->
      <!-- El nav#sidebar es position:fixed, NO necesita el div.sidebar wrapper -->
      <nav id="sidebar">
            <div id="dismiss"><i class="fa fa-arrow-left"></i></div>
            <ul class="list-unstyled components">
               <li class="<?= $current === 'index.php' ? 'active' : '' ?>">
                  <a href="<?= $prefix ?>index.php">
                     <i class="fa fa-home"></i> Inicio
                  </a>
               </li>
               <li class="<?= $current === 'creditos.php' ? 'active' : '' ?>">
                  <a href="<?= $prefix ?>index.php#creditos">
                     <i class="fa fa-diamond"></i> Comprar Créditos
                  </a>
               </li>
               <li class="<?= $current === 'carrito.php' ? 'active' : '' ?>">
                  <a href="<?= $prefix ?>carrito.php">
                     <i class="fa fa-shopping-cart"></i> Carrito
                     <?php if ($cart_count > 0): ?>
                        <span style="background:#bc883d;color:#000;border-radius:10px;
                                     padding:1px 8px;font-size:12px;margin-left:6px;font-weight:bold">
                           <?= $cart_count ?>
                        </span>
                     <?php endif; ?>
                  </a>
               </li>
               <?php if (!empty($_SESSION['usuario_id'])): ?>
               <li class="<?= $current === 'checkout.php' ? 'active' : '' ?>">
                  <a href="<?= $prefix ?>checkout.php">
                     <i class="fa fa-credit-card"></i> Pagar
                  </a>
               </li>
               <li>
                  <a href="<?= $prefix ?>logout.php">
                     <i class="fa fa-sign-out"></i> Cerrar Sesión
                  </a>
               </li>
               <?php else: ?>
               <li class="<?= $current === 'login.php' ? 'active' : '' ?>">
                  <a href="<?= $prefix ?>login.php">
                     <i class="fa fa-sign-in"></i> Login
                  </a>
               </li>
               <?php endif; ?>
               <li>
                  <a href="<?= $prefix ?>index.php#contact">
                     <i class="fa fa-envelope"></i> Contacto
                  </a>
               </li>
            </ul>
      </nav>

      <div id="content" style="width:100%">
         <!-- ===== HEADER (cs.zip original) ===== -->
         <header>
            <div class="head_top">
               <div class="header">
                  <div class="container-fluid">
                     <div class="row">
                        <div class="col-md-3 logo_section">
                           <div class="full">
                              <div class="center-desk">
                                 <div class="logo">
                                    <!-- Logo Brazzino — reemplaza el logo original -->
                                    <a href="<?= $prefix ?>index.php" style="font-size:32px;font-weight:900;
                                       letter-spacing:4px;text-transform:uppercase;
                                       background-image:linear-gradient(to bottom,#bc883d,#f5e47b,#bc883d);
                                       -webkit-background-clip:text;-webkit-text-fill-color:transparent">
                                       ♦ BRAZZINO
                                    </a>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="right_header_info">
                              <ul>
                                 <!-- Carrito con contador -->
                                 <li class="menu_iconb">
                                    <a href="<?= $prefix ?>carrito.php" style="position:relative">
                                       <i class="fa fa-shopping-cart"></i>
                                       <?php if ($cart_count > 0): ?>
                                          &nbsp;(<?= $cart_count ?>)
                                       <?php else: ?>
                                          &nbsp;Carrito
                                       <?php endif; ?>
                                    </a>
                                 </li>
                                 <?php if (!empty($_SESSION['usuario_id'])): ?>
                                 <li class="menu_iconb">
                                    <a href="<?= $prefix ?>checkout.php">
                                       <i class="fa fa-credit-card"></i>&nbsp;Pagar
                                    </a>
                                 </li>
                                 <li>
                                    <a href="<?= $prefix ?>logout.php"
                                       style="color:#f5e47b;font-size:14px">
                                       <i class="fa fa-sign-out"></i>
                                       <?= htmlspecialchars($_SESSION['nombre'] ?? 'Salir') ?>
                                    </a>
                                 </li>
                                 <?php else: ?>
                                 <li class="menu_iconb">
                                    <a href="<?= $prefix ?>login.php">Login</a>
                                 </li>
                                 <?php endif; ?>
                                 <!-- Botón menú hamburguesa -->
                                 <li>
                                    <button type="button" id="sidebarCollapse">
                                       <img src="<?= $prefix ?>images/menu_icon.png" alt="Menú">
                                    </button>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </header>
         <!-- end header -->
