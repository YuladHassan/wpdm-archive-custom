<?php


namespace WPDM\AddOn\ArchivePage\Templates\SimpleSearch;


use WPDM\__\Template;

class SimpleSearch
{
    function __construct()
    {
        add_shortcode("wpdm_simple_search", [$this, 'render']);
    }

    function render($params = [])
    {
        global $wpdb;
        @extract($params);
        $link_template = isset($template) ? $template : 'link-template-calltoaction3';
        $items_per_page = isset($items_per_page) ? $items_per_page : 0;
        $init = isset($init) ? (int)$init : 0;

        ob_start();
        include Template::locate("simple-search-form.php", 'wpdm-archive-page/simplesearch', __DIR__.'/views');
        $html = ob_get_clean();

        return str_replace(array("\r","\n"),"",$html);
    }
}
