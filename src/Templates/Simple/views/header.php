<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<form id="srcp" style="margin-bottom: 10px">
    <div class="row">
        <input type="hidden" name="category" id="initc" value="<?php echo  $category; ?>" />
        <div class="col-md-6">
            <label for="src"><?php echo  __('Search','wpdm-archive-page'); ?>:</label>
            <div class="input-group input-src">
                <input type="text" class="form-control" name="src" placeholder="<?php echo  __('Search','wpdm-archive-page'); ?>" id="src">
                <div class="input-group-append input-group-btn">
                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
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
