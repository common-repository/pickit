<?php
use Ecomerciar\Pickit\Helper\Helper;
$pickit_processing = helper::get_assets_folder_url()."/img/processing.png";
$pickit_error_img = helper::get_assets_folder_url()."/img/error.png";
$has_token = $args['has_token'];
$href = $args['href'];
$sameTarget = $args['sameTarget'];
?>
<div id="pickit-error" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <img src="<?php echo esc_url($pickit_error_img)?>">
    <h2><?php echo __('No fue posible iniciar sesión', 'wc-pickit')?></h2>
    <p><?php echo __('Por favor revisa tu configuración.', 'wc-pickit')?></p>
    <a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=wc-settings&tab=shipping&section=pickit_shipping_options'))?>">
        <button> <?php echo __('Ir a ajustes', 'wc-pickit')?> 
        </button>
    </a>        
  </div>
</div>

<div class="pickit">
    <div class="pickit-container">
        <h2> <?php echo __('Iniciando sesión', 'wc-pickit')?> </h2>
        <p> <?php echo __('Por favor espere...', 'wc-pickit')?></p>
        <img src="<?php echo esc_url($pickit_processing)?>">
    </div>
</div>

<script>
    jQuery(document).ready(function(){       
        jQuery("span.close", jQuery("#pickit-error")).click( function() {
            jQuery("#pickit-error").css("display", 'none'); 
        });  
        <?php if(!$has_token){?>
            jQuery("#pickit-error").css("display", 'block');
        <?php } else { ?>
            <?php if($sameTarget) { ?>
                window.location.replace("<?php echo esc_url($href)?>");
            <?php } else { ?>
                var win = window.open("<?php echo esc_url($href)?>", '_blank');                               
            <?php } ?>           
        <?php } ?>
    });
</script>

