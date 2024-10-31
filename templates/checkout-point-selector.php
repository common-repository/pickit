<?php
use Ecomerciar\Pickit\Helper\Helper;

$pickit_logo = helper::get_assets_folder_url()."/img/pickit_isologo.png";
$points = $args['points'];
$id = "pickit-point-selection-" . $args['method-id'];
?>

<div id="<?php echo esc_attr($id);?>">
    <div class="modal pickit-point-selection">  
    <div class="modal-content">
        <span class="close">&times;</span>
        <img src="<?php echo esc_url($pickit_logo)?>">
        <h2><?php echo __('Seleccione el punto de retiro', 'wc-pickit')?></h2>

        <div class="three-col">
        <?php
            foreach($points as $key=>$values){
            ?>
            <div class="row">
                <div class="column"> <?php echo esc_html($values['name']) ?> </div>
                <div class="column"> <?php echo esc_html($values['address']) ?> </div>
                <div class="column">
                    <a class="button pickit-point-select-act" data-id="<?php echo esc_attr($key);?>" data-address="<?php echo esc_attr($values['address']);?>" > <?php echo __("Seleccionar" , 'wc-pickit'); ?> </a>
                </div>
            </div>
            <?php
            }
        ?>
        </div>
    </div>
    </div>
    <p>
        <span class="pickit-point-label"></span>
    </p>
    <a class="button pickit-point-selection-btn">
        <span>
        <?php echo __('Seleccionar Punto', 'wc-pickit'); ?>
        </span>
    </a>
    <p class="validate-required" id="pickit_point_field">
        <span class="woocommerce-input-wrapper">
            <input type="hidden" name="pickit_point" id="pickit_point" value="none">  
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

        jQuery(".pickit-point-select-act", ctx).click(function(){
            var id = jQuery(this).attr('data-id');
            var address = jQuery(this).attr('data-address');
            
            jQuery('input[name="pickit_point"]' , ctx ).attr('value', id);
            
            jQuery('span.pickit-point-label', ctx ).html(address);
            jQuery(".pickit-point-selection" , ctx).css("display", 'none'); 
        });

    });
</script>