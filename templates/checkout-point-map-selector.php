<?php
use Ecomerciar\Pickit\Helper\Helper;

$pickit_logo = helper::get_assets_folder_url()."/img/pickit_isologo.png";
//$points = $args['points'];
$id = "pickit-point-selection-" . $args['method-id'];
$url = Helper::get_current_map_lightbox_url() . $args['uuid'];
$pickit_selected_point = $args['pickit_point'];
$pickit_selected_point_name = $args['pickit_point_name'];
?>

<div id="<?php echo esc_attr($id);?>">
    <div class="modal pickit-point-selection">  
    <div class="modal-content">
        <span class="close">&times;</span>
        <iframe src="<?php echo esc_url($url);?>" width="100%" height="90%"></iframe>  
    </div>
    </div>

    <p>
        <span class="pickit-point-label"><?php echo esc_html($pickit_selected_point_name);?></span>
    </p>
    <a class="button pickit-point-selection-btn">
        <span>
        <?php echo __('Seleccionar Punto', 'wc-pickit'); ?>
        </span>
    </a>
    <p class="validate-required update_totals_on_change" id="pickit_point_field">
        <span class="woocommerce-input-wrapper">
            <input type="hidden" name="pickit_point" id="pickit_point" value="<?php echo esc_attr($pickit_selected_point);?>">  
        </span>
    </p>
</div>
<script>
    jQuery(document).ready( function(){
        var ctx = jQuery("#<?php echo esc_attr($id);?>");              
        // When the user clicks on <span> (x), close the modal
        jQuery("span.close", jQuery(".pickit-point-selection", ctx)).click( function() {
            jQuery(".pickit-point-selection", ctx).css("display", 'none'); 
        });      
        jQuery(".pickit-point-selection-btn", ctx).click(function(){
            jQuery(".pickit-point-selection", ctx).css("display", 'inherit'); 
        });
     
        window.addEventListener('message', function (event) {
            try{            
            var data=JSON.parse(event.data);
            if(data && data.action=='pointSelected') {                                     
               // jQuery('span.pickit-point-label', ctx ).html(data.point.nombre);
               // jQuery('input[name="pickit_point"]' , ctx ).attr('value', data.point.idPunto);
                jQuery(".pickit-point-selection" , ctx).css("display", 'none'); 
                
                console.log("selected point");
                console.log(data);
                //Update Total After Pickit Point Selection                                
                var dataToSend = {
                    action: "pickit_action_update_shipping_total_pp",           
                    nonce: "<?php echo wp_create_nonce('wc-pickit')?>",
                    pickit_point_selected: data.point.idPunto,
                    pickit_point_name_selected: data.point.nombre
                }
          
                jQuery.post("<?php echo esc_url( admin_url('admin-ajax.php') ); ?>", dataToSend, function (data) {
                    if (data.success) {                       
                        jQuery('body').trigger('update_checkout');
                    } else {
                        console.log(data);
                    }             
                });    
            }
        }
        catch(e){}
    }, false);
    });
</script>