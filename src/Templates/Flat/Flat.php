<?php


namespace WPDM\AddOn\ArchivePage\Templates\Flat;


use WPDM\__\Crypt;
use WPDM\__\Template;

class Flat
{
    function __construct()
    {
        add_shortcode("wpdm_archive_flat", [$this, 'render']);

        add_action("wp_ajax_load_ap_content", [$this, 'APSContent']);
        add_action("wp_ajax_nopriv_load_ap_content", [$this, 'APSContent']);

    }

    /**
     * @usage Category and Package Details Template for Archive Page Sidebar View
     */
    function APSContent(){
        $params = Crypt::decrypt(wpdm_query_var('scparams'), true);
        $parent = wpdm_valueof($params, 'category', '');
        $parent = $parent && term_exists($parent, 'wpdmcategory') ? get_term_by('slug', $parent, 'wpdmcategory')->term_id : 0;
        if(isset($_POST['cid']))
            include(wpdm_tpl_path('aps-content-cat.php', 'wpdm-archive-page/flat', __DIR__.'/views'));
        if(isset($_POST['pid']))
            include(wpdm_tpl_path('aps-content-pack.php', 'wpdm-archive-page/flat', __DIR__.'/views'));
        die();
    }

    function render($params = [])
    {
        $cat_orderby = wpdm_valueof($params, 'cat_orderby', 'name');
        $cat_order = wpdm_valueof($params, 'cat_order', 'ASC');
        $showcount = wpdm_valueof($params, 'showcount', 0);
        $parent = wpdm_valueof($params, 'category', '');
        $parent = $parent && term_exists($parent, 'wpdmcategory') ? get_term_by('slug', $parent, 'wpdmcategory')->term_id : 0;


        if (wpdm_valueof($params, 'login', ['default' => 0, 'validate' => 'int']) === 1 && !is_user_logged_in())
            return wpdm_login_form(array('redirect' => $_SERVER['REQUEST_URI']));

        $order_fields = explode("|", wpdm_valueof($params, 'order_fields'));

        /**
         * Shortcode ID
         */
        $scid = wpdm_valueof($params, 'scid', ['default' => uniqid()]);

        ob_start();
        include Template::locate("flat.php", 'wpdm-archive-page/flat', __DIR__ . '/views');
        wp_reset_query();
        return ob_get_clean();
    }

    /**
     * @param int $parent
     * @param string $btype
     * @param int $base
     * @usage Render WPDM Category List
     */
    function renderCats($parent = 0, $btype = 'secondary', $base = 0, $showcount = 1, $params = array())
    {
        global $wpdb, $current_user;
        $user_role = isset($current_user->roles[0]) ? $current_user->roles[0] : 'guest';
        $args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
            'exclude' => array(),
            'exclude_tree' => array(),
            'include' => array(),
            'number' => '',
            'fields' => 'all',
            'slug' => '',
            'parent' => $parent,
            'hierarchical' => true,
            'get' => '',
            'name__like' => '',
            'pad_counts' => false,
            'offset' => '',
            'search' => '',
            'cache_domain' => 'core'
        );

        $args = is_array($params) && count($params) > 0 ? array_merge($args, $params) : $args;
        $categories = get_terms('wpdmcategory', $args);

        if (is_array($categories)) {

            if ($parent != $base)
                echo "<ul class='wpdm-dropdown-menu collapse'  id='collapse-{$parent}'>";

            foreach ($categories as $category) {
                if (WPDM()->package->userCanAccess($category->term_id, 'category') || get_option('_wpdm_hide_all', 0) == 0) {
                    $cld = get_term_children($category->term_id, 'wpdmcategory');
                    $ccount = $category->count;
                    $link = get_term_link($category);
                    ?>

                    <li class="wpdm-cat-item">
                    <div class="btn-group text-left d-flex" style="width: 100%">
                        <a style="width: <?php echo (count($cld) > 0) ? 'calc(100% - 44px);border-radius: 3px 0 0 3px' : '100%'; ?>"
                           class="wpdm-cat-link text-left btn  btn-<?php echo $btype; ?>"
                           rel='<?php echo $category->term_id; ?>' href="<?php echo $link; ?>">
                            <?php echo stripcslashes($category->name); ?><?php if ((int)$showcount == 1) echo " ($ccount)"; ?>
                        </a>
                        <?php if (count($cld) > 0): ?>
                            <a style="width: 44px;border-radius: 0 3px 3px 0" class=" btn  btn-<?php echo $btype; ?> btn-clps"
                               data-toggle="collapse" href="#collapse-<?php echo $category->term_id; ?>" role="button"
                               aria-expanded="false" aria-controls="collapse-<?php echo $category->term_id; ?>">
                                <i class="fa fa-chevron-down"></i>
                            </a>
                        <?php endif; ?>
                    </div>


                    <?php $this->renderCats($category->term_id, $btype, $base, $showcount, $params);

                    echo '</li>';
                }
            }

            if ($parent != $base) echo "</ul>";

        }
    }

}
