<?php

function WPDMAC()
{
    global $ArchivePages;
    return $ArchivePages;
}

/**
 * Common use function. Generates order by dropdown
 * @param $order_fields
 * @param $selected
 */
function wpdmapc_orderby($order_fields, $selected)
{
    if (is_array($order_fields) && count($order_fields) > 1) {
        foreach ($order_fields as &$order_field) {
            $order_field = explode(":", $order_field);
            $_order_fields[$order_field[0]] = $order_field[1];
        }
        $order_fields = $_order_fields;
    } else {
        $order_fields = array(
            'date' => __('Publish Date', 'wpdm-archive-page'),
            'title' => __('Title', 'wpdm-archive-page'),
            'modified' => __('Last Updated', 'wpdm-archive-page'),
            'view_count' => __('View Count', 'wpdm-archive-page'),
            'download_count' => __('Download Count', 'wpdm-archive-page'),
            'package_size_b' => __('Package Size', 'wpdm-archive-page')
        );
    }
    ?>
    <select name="orderby" id="orderby" class="form-control wpdm-custom-select">
        <?php foreach ($order_fields as $field => $label){ ?>
            <option value="<?php echo $field ?>" <?php selected($selected, $field); ?>><?php echo $label; ?></option>
        <?php } ?>
    </select>
    <?php
}
