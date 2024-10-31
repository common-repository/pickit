<?php
$href = $args['href'];
$label = $args['label'];
?>
<script>
    jQuery(document).ready(function(){
        if( ! jQuery('#pickit-gotopanel').length ){
            var btn = jQuery('<a href="<?php echo esc_url($href)?>" id="pickit-gotopanel" class="page-title-action" target="_blank"><?php echo __($label)?></a>');
        jQuery(btn).insertBefore(jQuery(".wp-header-end"));
        }        
    });
</script>