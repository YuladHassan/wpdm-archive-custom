<?php
use WPDM\__\__;

if(!defined("ABSPATH")) die("Shit happens!");
$terms = get_terms('wpdmcategory', array('hide_empty' => false,'parent' => $parent));
?>
    <div class="w3eden">
        <div class="row">
            <?php if(count($terms) > 0) { ?>
            <div class="col-md-3">
                <div class="list-group apc-sidebar">
                    <!--<a class="list-group-item apc-item-642fb4f05dd74 apc-home" href="/archive-page-flat/#all" data-item-id="45"><i class="fas fa-home mr-3"></i><?php /*= __('All Downloads', 'wpdm-archive-page') */?></a>-->
                    <?php


                    $xid = uniqid();



                    foreach ($terms as $term) {

                        // 1. Skip if current user does not have access to this category ( Manage role based category access from category settings page )
                        // 2. If following line is active, category roles access must be defined or it will not be listed
                        //if( ! wpdm_user_has_access( $term->term_id, 'category' ) ) continue;

                        echo "<a class='list-group-item apc-item-{$xid} apc-cat-{$term->term_id}' href='{$_SERVER['REQUEST_URI']}#{$term->slug}/{$term->term_id}' data-item-id='{$term->term_id}'><i class=\"fas fa-folder mr-3\"></i>{$term->name}</a>";
                    }
                    ?>
                </div>
            </div>
            <?php } ?>
            <div class="col-md-<?= (count($terms) > 0) ? 9: 12 ?>">
                <div class="wpdm-loading" id="wpdm-loading-<?php echo $xid; ?>" style="border-radius:0;display:none;right:15px;line-height: 50px;padding: 0 30px;"><i class="fa fa-sun fa-spin"></i> <?php _e('Loading...','wpdm-archive-page'); ?></div>
                <div id="ap-content-<?php echo $xid; ?>">


                    <div class="breadcrumb fetfont" style="border-radius:0;">
                        <?php _e('Newest Items','wpdm-archive-page'); ?>
                    </div>

                    <table class="table table-border table-striped">

                        <?php
                        global $post;
                        $cparams['posts_per_page'] = isset($params['items_per_page']) && (int)$params['items_per_page'] > 0? $params['items_per_page'] : 10;
                        $cparams['post_type'] = 'wpdmpro';
                        if($parent > 0) {
                            $cparams['tax_query'] = array(array(
                                'taxonomy' => 'wpdmcategory',
                                'include_children' => true,
                                'field' => 'term_id',
                                'terms' => [$parent]
                            ));
                        }

                        //order parameter
                        $orderby = isset($params['orderby'])? $params['orderby'] : 'date';
                        $order    = isset($params['order'])? $params['order'] : 'desc';

                        if(isset($orderby) && $orderby != '') {
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
                                <td><img src="<?php echo $icon; ?>" style="float: left;width: 20px;margin: 0 10px 0 0;" /> <?php the_title(); ?></td>
                                <td><?php echo get_the_modified_date(); ?></td>
                                <td class="text-right" style="white-space: nowrap">
                                    <?= __::valueof($params, 'download', 'int') ? WPDM()->package->downloadLink(get_the_ID(), false, ['btnclass' => 'btn btn-xs btn-success mr-2']) : ''; ?>
                                    <?php if(!isset($params['details']) || (int)$params['details'] === 1) { ?>
                                    <a href="#" class="btn btn-xs btn-primary btn-apc-sidebar apc-pack-<?php echo $xid; ?>" data-item-id="<?php the_ID(); ?>"><?php _e('View Details', 'wpdm-archive-page'); ?></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <script>


        jQuery(function($){
            var wpdmap_params = '<?= \WPDM\__\Crypt::encrypt($params) ?>';
            $('body').on('click', '.apc-item-<?php echo $xid; ?>', function(e){
                e.preventDefault();
                $('#wpdm-loading-<?php echo $xid; ?>').fadeIn();
                $('.apc-item-<?php echo $xid; ?> i.fas').removeClass('fa-folder-open').addClass('fa-folder');
                $('.apc-cat-'+$(this).data('item-id')+' i.fas').removeClass('fa-folder').addClass('fa-folder-open');
                $('#ap-content-<?php echo $xid; ?>').load("<?php echo admin_url('admin-ajax.php'); ?>", {action:'load_ap_content', cid: $(this).data('item-id'), xid: '<?php echo $xid; ?>', orderby: '<?php echo $orderby; ?>', order: '<?php echo $order; ?>', scparams: wpdmap_params }, function(){ $('#wpdm-loading-<?php echo $xid; ?>').fadeOut(); });
                location.href = this.href;
                return false;
            });

            $('body').on('click', '.breadcrumb .folder', function(){
                $('#wpdm-loading-<?php echo $xid; ?>').fadeIn();
                $('.apc-cat-<?php echo $xid; ?> i.fas').removeClass('fa-folder-open').addClass('fa-folder');
                $('.apc-cat-'+$(this).data('cat')+' i.fas').removeClass('fa-folder').addClass('fa-folder-open');
                $('#ap-content-<?php echo $xid; ?>').load("<?php echo admin_url('admin-ajax.php'); ?>", {action:'load_ap_content', cid: $(this).data('cat'), xid: '<?php echo $xid; ?>', orderby: '<?php echo $orderby; ?>', order: '<?php echo $order; ?>', scparams: wpdmap_params}, function(){ $('#wpdm-loading-<?php echo $xid; ?>').fadeOut(); });
                return false;
            });

            $('body').on('click', '.apc-pack-<?php echo $xid; ?>', function(){
                $('#wpdm-loading-<?php echo $xid; ?>').fadeIn();
                $('#ap-content-<?php echo $xid; ?>').load("<?php echo admin_url('admin-ajax.php'); ?>", {action:'load_ap_content', pid: $(this).data('item-id'), pagetemplate: '<?php echo isset($params['page_template'])?$params['page_template']:''; ?>', xid: '<?php echo $xid; ?>', scparams: wpdmap_params}, function(){ $('#wpdm-loading-<?php echo $xid; ?>').fadeOut(); });
                return false;
            });

            var __wpdmap_flat_cat = '';
            if(location.href.split('#').length == 2) {
                __wpdmap_flat_cat = location.href.split('/');
                __wpdmap_flat_cat = __wpdmap_flat_cat[__wpdmap_flat_cat.length - 1];
                if(parseInt(__wpdmap_flat_cat) > 0) {
                    $('#wpdm-loading-<?php echo $xid; ?>').fadeIn();
                    $('.apc-item-<?php echo $xid; ?> i.fas').removeClass('fa-folder-open').addClass('fa-folder');
                    $('.apc-cat-'+__wpdmap_flat_cat+' i.fas').removeClass('fa-folder').addClass('fa-folder-open');
                    $('#ap-content-<?php echo $xid; ?>').load("<?php echo admin_url('admin-ajax.php'); ?>", {action:'load_ap_content', cid: __wpdmap_flat_cat, xid: '<?php echo $xid; ?>', orderby: '<?php echo $orderby; ?>', order: '<?php echo $order; ?>', scparams: wpdmap_params }, function(){ $('#wpdm-loading-<?php echo $xid; ?>').fadeOut(); });
                    return false;
                }
            }
        });
    </script>
    <style>
        .w3eden .breadcrumb {
            background: #f5f5f5;
            border-radius: 2px;
            padding: 0 15px;
            line-height: 50px;
            margin-bottom: 15px;
        }
        .w3eden .breadcrumb a,
        .w3eden .breadcrumb i{
            line-height: 50px;
            margin: 0 2px;
        }
        .w3eden .list-group-item .fas{
            margin-right: 5px;
        }
        .w3eden .list-group{
            margin-bottom: 15px;
        }
        .w3eden .list-group, .list-group-item{
            border-radius: 0 !important;
            position: relative;
        }
        .w3eden .table-border{
            border: 1px solid #dddddd;
        }
        .w3eden .table-border td img{
            box-shadow: none;
        }
        .w3eden .table-border td{
            padding: 10px 15px !important;
            vertical-align: middle !important;
        }
    </style>
