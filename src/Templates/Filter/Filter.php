<?php


namespace WPDM\AddOn\ArchivePage\Templates\Filter;


use WPDM\__\Template;

class Filter
{
    function __construct()
    {
        add_shortcode("wpdm_archive_filter", [$this, 'render']);
    }

    function render($params = [])
    {
        $cat_orderby = wpdm_valueof($params, 'cat_orderby', 'name');
        $cat_order = wpdm_valueof($params, 'cat_order', 'ASC');
        $showcount = wpdm_valueof($params, 'showcount', 0);
        $categories = wpdm_valueof($params, 'categories', '');
        $featured_cats = wpdm_valueof($params, 'featured_cats', '');
        $parent = wpdm_valueof($params, 'category', '');
        $parent = $parent && term_exists($parent, 'wpdmcategory') ? get_term_by('slug', $parent, 'wpdmcategory')->term_id : 0;
        $ex = wpdm_valueof($params, 'cat_style', '');
        if($ex) $ex = "-{$ex}"; 
        /**
         * Shortcode ID
         */
        $scid = wpdm_valueof($params, 'scid', ['default' => uniqid()]);

        if (wpdm_valueof($params, 'login', ['default' => 0, 'validate' => 'int']) === 1 && !is_user_logged_in())
            return wpdm_login_form(array('redirect' => $_SERVER['REQUEST_URI']));

        $order_fields = explode("|", wpdm_valueof($params, 'order_fields'));

        ob_start();
        include Template::locate("filter.php", 'wpdm-archive-page/filter',__DIR__ . '/views');
        wp_reset_query();
        return ob_get_clean();
    }


}
