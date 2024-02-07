<?php


namespace WPDM\AddOn\ArchivePage\Templates\Category;


use WPDM\__\Template;

class Blocks
{
    function __construct()
    {
        add_shortcode("wpdm_category_blocks", [$this, 'render']);
    }

    function render($params = [])
    {
        if(!isset($params['categories'])) return '';
        $categories = explode(",", $params['categories']);
        $_categories = array();
        foreach ($categories as $i => $category){
            if(trim($category) !== '' && term_exists($category, 'wpdmcategory'))
                $_categories[] = get_term_by('slug', $category, 'wpdmcategory');
        }
        $cols = isset($params['cols'])?$params['cols']:3;
        $grid = array(1 => 12, 2 => 6, 3 => 4, 4 => 3, 6 => 2);
        $grid_class = "col-md-".(isset($grid[$cols])?$grid[$cols]:4);
        $sec_id = isset($params['elid'])?$params['elid']:uniqid();
        $button_color = isset($params['button_color'])?$params['button_color']:'blue';
        $hover_color = isset($params['hover_color'])?$params['hover_color']:'blue';
        $hover_color = in_array($hover_color, array('green', 'blue', 'purple', 'primary', 'success', 'warning', 'danger', 'info'))?"var(--color-{$hover_color})":$hover_color;
        $button_color = in_array($button_color, array('green', 'blue', 'purple', 'primary', 'success', 'warning', 'danger', 'info', 'primary-hover', 'success-hover', 'warning-hover', 'danger-hover', 'info-hover'))?"var(--color-{$button_color})":$button_color;

        ob_start();
        $categories = $_categories;
        include Template::locate("category-blocks.php", 'wpdm-archive-page/category', __DIR__.'/views/');
        return ob_get_clean();
    }



}
