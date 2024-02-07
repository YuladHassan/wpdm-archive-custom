<?php
use WPDM\__\__;
use WPDM\Category\CategoryController;

$term = get_term((int)$_POST['cid'], 'wpdmcategory');

?>
<div class="breadcrumb" style="border-radius:0;">
    <?php
    if(!$parent)
        \WPDM\Category\CategoryController::CategoryBreadcrumb($term->term_id,0);
    else
        echo "<a href='#' class='folder' data-cat='{$parent}'>".esc_attr__( 'Home', WPDMAP_TEXT_DOMAIN )."</a>";
    ?>
</div>

<?php if(wpdm_query_var('cid')) { ?>
<div class="list-group">
<?php

$terms = get_terms('wpdmcategory', array('parent'=>wpdm_query_var('cid'), 'hide_empty' => false));
$xid = wpdm_query_var('xid');
foreach($terms as $term){
	echo "<a class='list-group-item apc-item-{$xid} apc-cat-{$term->term_id}' href='#{$term->slug}/{$term->term_id}' data-item-id='{$term->term_id}'><i class=\"fas fa-folder mr-3\"></i>{$term->name}</a>";
	//echo "<a class='list-group-item apc-item-".esc_attr(strip_tags($_REQUEST['xid']))."' href='#' data-item-id='{$term->term_id}'>{$term->name}</a>";
}
?>
</div>
<?php } ?>

<table class="table table-border table-striped">

    <?php
    global $post;
    $cparams['posts_per_page'] = -1;
    $cparams['post_type'] = 'wpdmpro';
    if(wpdm_query_var('cid')) {
        $include_children = $parent === wpdm_query_var('cid', 'int') ? true : false;
        $cparams['tax_query'] = array(array(
            'taxonomy' => 'wpdmcategory',
            'field' => 'term_id',
            'include_children' => $include_children,
            'terms' => [wpdm_query_var('cid', 'int')]
        ));
    }
    //order parameter
    $order = isset($_REQUEST['order']) ? addslashes(esc_attr($_REQUEST['order'])) : 'desc';
    $orderby = isset($_REQUEST['orderby']) ? addslashes(esc_attr($_REQUEST['orderby'])) : 'date';

    if($orderby !== '') {
        //order parameter
        if($orderby == 'view_count' || $orderby == 'download_count' || $orderby == 'package_size_b'){
            $cparams['meta_key'] = '__wpdm_' . $orderby;
            $cparams['orderby'] = 'meta_value_num';
        }
        else {
            $cparams['orderby'] = $orderby;
        }
        if($order == '') $order = 'ASC';
        $cparams['order'] = $order;

    }

    $packs = new WP_Query($cparams);

    while( $packs->have_posts() ){
        $packs->the_post();

        if( !wpdm_user_has_access( get_the_ID() ) && (int)get_option('_wpdm_hide_all', 0) === 1) continue;

        $icon = get_post_meta( get_the_ID(), '__wpdm_icon', true );
        $icon = ( $icon == '' ) ? WPDM_BASE_URL.'assets/file-type-icons/wpdm.svg' : $icon;
        if(strpos($icon, 'file-type-icons/') && !strpos($icon, 'assets/file-type-icons/')) $icon = str_replace('file-type-icons/', 'assets/file-type-icons/', $icon);
            ?>
            <tr>
                <td><img src="<?php echo $icon; ?>" style="float: left;margin-right: 10px;width: 20px;" /> <?php the_title(); ?></td>
                <td><?php echo get_the_modified_date(); ?></td>
                <td class="text-right">
                    <?= __::valueof($params, 'download', 'int') ? WPDM()->package->downloadLink(get_the_ID(), false, ['btnclass' => 'btn btn-xs btn-success mr-2']) : ''; ?>
                    <?php if(!isset($params['details']) || (int)$params['details'] === 1) { ?>
                        <a href="#" class="btn btn-xs btn-primary btn-apc-sidebar apc-pack-<?php echo wpdm_query_var('xid', 'txt'); ?>" data-item-id="<?php the_ID(); ?>"><?php _e('View Details', 'wpdm-archive-page'); ?></a>
                    <?php } ?>
                </td>
            </tr>
        <?php
    }
    ?>
</table>
