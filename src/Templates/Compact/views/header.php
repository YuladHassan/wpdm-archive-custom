<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<form id="srcp" style="margin-bottom: 10px">
    <div class="row">
        <input type="hidden" name="category" id="initc" value="<?php echo  $parent; ?>" />
        <div class="col-md-3">
            <label for="src"><?php echo  __('Search','wpdm-archive-page'); ?>:</label>
            <div class="input-group input-src">
                <input type="text" class="form-control" name="src" placeholder="<?php echo  __('Search','wpdm-archive-page'); ?>" id="src">
                <div class="input-group-append input-group-btn">
                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>

        <div class='col-md-3'>
            <label for="wpdm-cats-compact"><?php _e('Category:','wpdm-archive-page'); ?></label>
            <?php
            $_params = array('show_option_none' => __( "Select category" , "download-manager" ), 'hierarchical' => true, 'show_count' => 0, 'orderby' => $cat_orderby, 'order' => $cat_order, 'echo' => true, 'class' => 'form-control wpdm-custom-select', 'taxonomy' => 'wpdmcategory', 'hide_empty' => 0, 'name' => 'wpdm-cats-compact', 'id' => 'wpdm-cats-compact', 'selected' => '');
            if($parent > 0) $_params['parent'] = $parent;
            wp_dropdown_categories($_params);
            ?>
        </div>

        <div class="col-md-3">
            <label for="orderby"><?php echo  __('Order By:','wpdm-archive-page'); ?></label>
            <?php wpdmapc_orderby($order_fields, wpdm_valueof($params, 'orderby')); ?>
        </div>
        <div class="col-md-3">
            <label for="order"><?php echo  __('Order:','wpdm-archive-page'); ?></label>
            <select name="order" id="order" class="form-control wpdm-custom-select">
                <option value="DESC"><?php echo  __('Descending Order','wpdm-archive-page'); ?></option>
                <option value="ASC" <?php selected('ASC', strtoupper(wpdm_valueof($params, 'order'))) ?>><?php echo  __('Ascending Order','wpdm-archive-page'); ?></option>
            </select>
        </div>

    </div>
</form>
