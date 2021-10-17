<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
?>

<style>
input.tnp-email {
    background-color: #ffffff!important;
}

.tnp-subscription {
    font-size: 13px;
    display: block;
    margin: 0!important;
    max-width: 500px;
    width: 100%;
    font-family: 'Montserrat', sans-serif;
}

.tnp-field input.tnp-submit {
    font-weight: 400;
    background-color: #e5502d !important;
    border-color: #e5502d;
    font-size: 18px;
    font-family: 'Anton', sans-serif;
    color: #fff !important;
    width: auto !important;
    height: auto !important;
    padding: 20px 40px !important;
    text-transform: uppercase;
    border-radius: .25rem;
    cursor: pointer;
}

input.tnp-email {
    display: block;
    width: 100%;
    height: calc(1.5em + .75rem + 2px)!important;
    padding: .375rem .75rem!important;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057!important;
    background-color: #fff!important;
    background-clip: padding-box;
    border: 1px solid #ced4da!important;
    border-radius: .25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

input.tnp-email::placeholder{
  color: #000!important;
  letter-spacing: 1px!important;
}

ul#menu-menu-footer {
    font-size: 1rem;
    font-weight: 400;
    padding: .75em 0;
    letter-spacing: 1px;
    text-decoration: none;
    font-family: Montserrat,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji';
    text-transform: uppercase;
}
</style>
<section class="py-5 feed-social">
  <?php echo do_shortcode('[instagram-feed]');?>
  </section>
<section class="page-section newsletter" id="newsletter">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 text-left">
		  <img src="<?php bloginfo('template_directory');?>/img/logos/footer_astor.png" style="width: 70%" class="img-fluid" alt="productos astor" googl="true">
		  <p><a href="mailto:contacto@astor.com.mx">contacto@astor.com.mx</a><br>
             222-753-5999</p>
          <?php
            $args = array(
              'theme_location'  =>  'menu-footer',
              'container' => 'nav',
              'container_class' => 'nav-foot'
            );

            wp_nav_menu($args);
          ?>
        </div>

        <div class="col-lg-6 news">
          <h2 class="section-heading text-uppercase">Únete al movimiento fit</h2>
          <h3 class="section-subheading text-orange">Recíbe todas nuestras noticias, contenidos, ofertas, etc...</h3>
          <div class="tnp tnp-subscription">
            <form method="post" action="http://astor.digitaltryout.com/?na=s" onsubmit="return newsletter_check(this)">

              <input type="hidden" name="nlang" value="">
              <div class="tnp-field tnp-field-email">
                <input placeholder="Escribe tu email" class="tnp-email" type="email" name="ne" required>
              </div>
                <div class="tnp-field tnp-field-button"><input class="tnp-submit" type="submit" value="Enviar" >
              </div>
            </form>
          </div>
        </div>
		  
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-5">
          <span class="copyright">Copyright © Productos Astor 2020</span>
        </div>
        <div class="col-md-2">
          <ul class="list-inline social-buttons">
            <li class="list-inline-item">
              <a href="#">
                <i class="fab fa-instagram"></i>
              </a>
            </li>
            <li class="list-inline-item">
              <a href="#">
                <i class="fab fa-facebook-f"></i>
              </a>
            </li>
          </ul>
        </div>
        <div class="col-md-5">
          <ul class="list-inline quicklinks">
            <li class="list-inline-item">
              <a href="/envios-y-devoluciones/">Políticas de envíos y devoluciones</a>&nbsp;&nbsp; 
			  <a href="/politica-de-privacidad/">Avíso de privacidad</a>
            </li>

          </ul>
        </div>
      </div>
    </div>
  </footer>


</div><!-- #page we need this extra closing tag here -->

<?php wp_footer(); ?>
<script src="<?php bloginfo('template_directory');?>/js/scripts.js"></script>
</body>

</html>