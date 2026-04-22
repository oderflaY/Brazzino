         <!-- ===== FOOTER (cs.zip original — clases y estructura sin tocar) ===== -->
         <footer>
            <div class="footer">
               <div class="container">
                  <div class="row">
                     <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="address">
                           <h3>Suscríbete</h3>
                           <form class="newtetter" onsubmit="return false">
                              <button class="submit" type="button">Enviar</button>
                              <input class="tetter" placeholder="Tu correo electrónico" type="email">
                           </form>
                        </div>
                     </div>
                     <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                           <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                              <div class="address">
                                 <h3>Navegación</h3>
                                 <ul class="Menu_footer">
                                    <li><a href="index.php">Inicio</a></li>
                                    <li><a href="index.php#creditos">Comprar Créditos</a></li>
                                    <li><a href="carrito.php">Carrito</a></li>
                                    <li><a href="login.php">Acceso</a></li>
                                    <li><a href="index.php#contact">Contacto</a></li>
                                 </ul>
                              </div>
                           </div>
                           <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                              <div class="address">
                                 <h3>Contacto</h3>
                                 <ul class="Links_footer">
                                    <li>Brazzino Casino · Plataforma de entretenimiento electrónico</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="copyright">
                  <div class="container">
                     <p>Copyright <?= date('Y') ?> &copy; <strong>Brazzino Casino</strong> — Todos los derechos reservados. Juega con responsabilidad.</p>
                  </div>
               </div>
            </div>
         </footer>
         <!-- end footer -->

      </div><!-- end #content -->
   </div><!-- end .wrapper -->

   <div class="overlay"></div>

   <!-- JS ORIGINAL cs.zip — SIN MODIFICAR -->
   <script src="js/jquery.min.js"></script>
   <script src="js/popper.min.js"></script>
   <script src="js/bootstrap.bundle.min.js"></script>
   <script src="js/jquery-3.0.0.min.js"></script>
   <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
   <script src="js/custom.js"></script>

   <!-- Sidebar script original -->
   <script>
   $(document).ready(function() {
      $("#sidebar").mCustomScrollbar({ theme: "minimal" });
      $('#dismiss, .overlay').on('click', function() {
         $('#sidebar').removeClass('active');
         $('.overlay').removeClass('active');
      });
      $('#sidebarCollapse').on('click', function() {
         $('#sidebar').addClass('active');
         $('.overlay').addClass('active');
         $('.collapse.in').toggleClass('in');
         $('a[aria-expanded=true]').attr('aria-expanded','false');
      });
   });
   </script>
   <?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
