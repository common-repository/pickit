<?php
use Ecomerciar\Pickit\Helper\Helper;
use Ecomerciar\Pickit\Settings\Cron;

$pickit_woo_icons = helper::get_assets_folder_url()."/img/pickit-woo-icons.png";
$pickit_left_pane_img = helper::get_assets_folder_url()."/img/pickit-left-pane-img.png";
$pickit_ok_img = helper::get_assets_folder_url()."/img/ok.png";
$pickit_error_img = helper::get_assets_folder_url()."/img/error.png";
$countries = Helper::get_countries();
$register_result = $args['register'];
$contact_href = Helper::get_contact_href();

if ( isset($_POST['submit']) ){
    Cron::run_cron();
}
?>

<!-- The Modal -->
<div id="pickit-ok" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <img src="<?php echo esc_url($pickit_ok_img)?>">
    <h2><?php echo __('Credenciales correctas', 'wc-pickit')?></h2>
    <p><?php echo __('Has finalizado la configuración inicial para pickit.', 'wc-pickit')?></p>
    <p><?php echo __('Ahora necesitas <strong>configurar los puntos de retiro</strong>. Para esto, dirígete a <strong>WooCommerce > Ajustes > Envíos > Zonas de Envíos</strong>', 'wc-pickit')?></p>
    <a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=wc-settings&tab=shipping&section=pickit_shipping_options'))?>">
        <button> <?php echo __('Ir a ajustes', 'wc-pickit')?> 
        </button>
    </a>        
  </div>
</div>
<div id="pickit-error" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <img src="<?php echo esc_url($pickit_error_img)?>">
    <h2><?php echo __('Credenciales incorrectas', 'wc-pickit')?></h2>
    <p><?php echo __('Las credenciales ingresadas son incorrectas.<br>Por favor, vuelve a intentarlo.', 'wc-pickit')?></p>    
    <button><?php echo __('Aceptar', 'wc-pickit')?></button>
  </div>
</div>

<div class="pickit">

    <div class="pickit-container">
        <div class="row">
            <div class="column30">
                <div class="left-pane">
                    <img src="<?php echo esc_url($pickit_woo_icons)?>">
                    <h1 class="welcome"><?= sprintf(__('¡Hola <span class="pickit-sitename">@%s!</span>', 'wc-pickit'), get_option('blogname'))?> </h1>
                    <p><?php echo __("Gracias por comenzar a usar la aplicación de <strong>pickit</strong> para <strong>WooCommerce</strong>, Para continuar con el proceso de configuración, es necesario que ya cuentes con una cuenta en <strong>pickit</strong>. Si aún no la tienes, <a class='create_account_link' href='#' target='_blank'> Crea tu cuenta aquí.</a>", 'wc-pickit')?></p>
                    <img class="left-pane-img" src="<?php echo esc_url($pickit_left_pane_img)?>">
                </div>                        
            </div>
            <div class="column70">
                <div class="right-pane">
                    <h2><?php echo __('Ingresá tus credenciales de pickit', 'wc-picki')?></h2>
                    <p><?php echo __('Conectá tu cuenta de pickit con WooCommerce <br> ¿No conocés tus credenciales? <a class="contact_link" href="#" target="_blank" >Contáctanos</a>', 'wc-picki')?></p>
                    <br><br>
                    <form method="post" action="admin.php?page=wc-pickit-onboarding">
                        <?php settings_fields( 'wc-pickit-settings-onboarding' ); ?>
                        <?php do_settings_sections( 'wc-pickit-settings-onboarding' ); ?>
    
                        <label for="wc-pickit-api-key" required><?php echo __('API Key','wc-pickit') ?></label>
                        <?php woocommerce_form_field('wc-pickit-api-key', array(
                            'type' => 'text',
                            'required' => true,                        
                        ) , isset($_POST['wc-pickit-api-key'])? $_POST['wc-pickit-api-key'] : get_option( 'wc-pickit-api-key' ) );?>
                        
                        
                        <label for="wc-pickit-api-secret" required><?php echo __('Token Id','wc-pickit') ?></label>
                        <?php woocommerce_form_field('wc-pickit-api-secret', array(
                            'type' => 'password',
                            'required' => true,                        
                        ) , isset($_POST['wc-pickit-api-secret'])? $_POST['wc-pickit-api-secret'] : get_option( 'wc-pickit-api-secret' ) );?>
                        
                        
                        <label for="wc-pickit-api-country" required><?php echo __('País','wc-pickit')  ?></label>
                        <?php woocommerce_form_field('wc-pickit-api-country', array(
                            'type' => 'select',
                            'options' => $countries,
                            'required' => true,                        
                        ) , isset($_POST['wc-pickit-api-country'])? $_POST['wc-pickit-api-country'] : get_option( 'wc-pickit-api-country' ) );?>
                        
                        <?php submit_button( __( 'Ingresar', 'wc-pickit' ), 'primary-button' ); ?>
                    </form>
    
                </div>
            </div>
        </div>    
    </div>
</div>

<script>
    jQuery(document).ready( function(){              
        // When the user clicks on <span> (x), close the modal
        jQuery("span.close", jQuery("#pickit-ok")).click( function() {
            jQuery("#pickit-ok").css("display", 'none'); 
        });              
        // When the user clicks on <span> (x), close the modal
        jQuery("span.close", jQuery("#pickit-error")).click( function() {
            jQuery("#pickit-error").css("display", 'none'); 
        });  
        jQuery("button", jQuery("#pickit-error")).click( function() {
            jQuery("#pickit-error").css("display", 'none'); 
        });      
    });
</script>

<?php if("OK"===$register_result){?>
    <script>
    jQuery(document).ready(function(){
        console.log("OK");
        jQuery("#pickit-ok").css("display", 'block');
    });
    </script>
<?php }?>

<?php if("NOK"===$register_result){?>
    <script>
    jQuery(document).ready(function(){
        console.log("NOK");
        jQuery("#pickit-error").css("display", 'block');
    });
    </script>
<?php }?>

<script>
    jQuery( function( $ ) {

    var hrefs = [];
    hrefs['AR'] =  "<?php echo esc_attr(sanitize_email($contact_href['AR'])); ?>";
    hrefs['CL'] =  "<?php echo esc_attr(sanitize_email($contact_href['CL'])); ?>";
    hrefs['CO'] =  "<?php echo esc_attr(sanitize_email($contact_href['CO'])); ?>";  
    hrefs['PE'] =  "<?php echo esc_attr(sanitize_email($contact_href['PE'])); ?>";  
    hrefs['ME'] =  "<?php echo esc_attr(sanitize_email($contact_href['ME'])); ?>";  
    hrefs['UY'] =  "<?php echo esc_attr(sanitize_email($contact_href['UY'])); ?>";  
    hrefs['DFLT'] =  "<?php echo esc_attr(sanitize_email($contact_href['DFLT'])); ?>";
    
    function wcCountry( el ) {
            var anchor = $( 'a.contact_link' );                    
            if(hrefs[$( el ).val()]){
                anchor.attr( 'href', "mailto:"+hrefs[$( el ).val()]);
            } else {
                anchor.attr( 'href', "mailto:"+hrefs['DFLT']);
            }
        }

        $( document.body ).on( 'change', '#wc-pickit-api-country', function() {
            wcCountry( this );
        });
        
        $( '#wc-pickit-api-country' ).trigger( 'change' );

    });

</script>