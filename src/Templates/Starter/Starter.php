<?php


namespace WPDM\AddOn\ArchivePage\Templates\Starter;

use WPDM\__\Template;

class Starter
{
    function __construct()
    {
        add_shortcode("wpdm_archive", [$this, 'render']);
    }

    function render($params = [])
    {
        $cat_orderby = wpdm_valueof($params, 'cat_orderby', 'name');
        $cat_order = wpdm_valueof($params, 'cat_order', 'ASC');
        $showcount = wpdm_valueof($params, 'showcount', 0);
        $parent = wpdm_valueof($params, 'category', '');
        $last_state = wpdm_valueof($params, 'last_state', ['validate' => 'int']);
        $parent = $parent && term_exists($parent, 'wpdmcategory') ? get_term_by('slug', $parent, 'wpdmcategory')->term_id : 0;


        if (wpdm_valueof($params, 'login', ['default' => 0, 'validate' => 'int']) === 1 && !is_user_logged_in())
            return wpdm_login_form(array('redirect' => $_SERVER['REQUEST_URI']));

        $order_fields = explode("|", wpdm_valueof($params, 'order_fields'));

        /**
         * Shortcode ID
         */
        $scid = wpdm_valueof($params, 'scid', ['default' => uniqid()]);

        ob_start();
        include Template::locate("starter.php", 'wpdm-archive-page/starter', __DIR__ . '/views');
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
                    $xpath = [$category->slug];
                    $parents = [$category->term_id];
                    $pcat = $category;
                    while ($pcat->parent > 0) {
                        $pcat = get_term($pcat->parent, 'wpdmcategory');
                        $xpath[] = $pcat->slug;
                        $parents[] = $pcat->term_id;
                    }
                    $parents = array_reverse($parents);
                    $xpath = array_reverse($xpath);
                    $xpath = "/".implode("/", $xpath);
                    $parents = implode("/", $parents);
                    ?>

                    <li class="wpdm-cat-item">
                    <div class="btn-group text-left d-flex" style="width: 100%">
                        <a style="width: <?php echo (count($cld) > 0) ? 'calc(100% - 44px);border-radius: 3px 0 0 3px' : '100%'; ?>"
                           class="wpdm-cat-link text-left btn  btn-<?php echo $btype; ?>"
                           data-path="<?php echo $xpath; ?>"
                           id='wpdm-cat-link-<?php echo str_replace("/", "_", $xpath); ?>'
                           rel='<?php echo $category->term_id; ?>' href="<?php echo $link; ?>">
                            <?php echo stripcslashes($category->name); ?><?php if ((int)$showcount == 1 && (int)$ccount > 0) echo " ($ccount)"; ?>
                        </a>
                        <?php if (count($cld) > 0): ?>
                            <a style="width: 44px;border-radius: 0 3px 3px 0" class=" btn  btn-<?php echo $btype; ?> btn-clps"
                               data-parents="<?php echo $parents; ?>"
                               id="clspt-<?php echo $category->term_id; ?>"
                               data-toggle="collapse" data-cid="<?php echo $category->term_id; ?>" href="#collapse-<?php echo $category->term_id; ?>" role="button"
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


