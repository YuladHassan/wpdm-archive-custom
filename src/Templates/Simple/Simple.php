<?php


namespace WPDM\AddOn\ArchivePage\Templates\Simple;

use WPDM\__\Template;

class Simple
{
    function __construct()
    {
        add_shortcode("wpdm_archive_simple", [$this, 'render']);
    }

    function render($params = [])
    {
        $cat_orderby = wpdm_valueof($params, 'cat_orderby', 'name');
        $cat_order = wpdm_valueof($params, 'cat_order', 'ASC');
        $showcount = wpdm_valueof($params, 'showcount', 0);


        if (wpdm_valueof($params, 'login', ['default' => 0, 'validate' => 'int']) === 1 && !is_user_logged_in())
            return wpdm_login_form(array('redirect' => $_SERVER['REQUEST_URI']));

        $order_fields = explode("|", wpdm_valueof($params, 'order_fields'));

        /**
         * Shortcode ID
         */
        $scid = wpdm_valueof($params, 'scid', ['default' => uniqid()]);

        ob_start();
        include Template::locate("simple.php", 'wpdm-archive-page/simple', __DIR__ . '/views');
        wp_reset_query();
        return ob_get_clean();
    }



}
