<?php


namespace WPDM\AddOn\ArchivePage\Templates\Category;


class Categories
{
    function __construct()
    {
        add_shortcode("wpdm_categories", [$this, 'render']);
    }

    function render($params = [])
    {
        global $wpdb;
        @extract($params);
        $parent = isset($parent)?$parent:0;
        $args = array(
            'orderby'       => 'name',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'exclude'       => array(),
            'exclude_tree'  => array(),
            'include'       => array(),
            'number'        => '',
            'fields'        => 'all',
            'slug'          => '',
            'parent'         => $parent,
            'hierarchical'  => false,
            'child_of'      => 0,
            'get'           => '',
            'name__like'    => '',
            'pad_counts'    => false,
            'offset'        => '',
            'search'        => '',
            'cache_domain'  => 'core'
        );
        $categories = get_terms('wpdmcategory',$args);
        $pluginsurl = plugins_url();
        $cols = isset($cols) && $cols > 0 ? $cols : 2;
        $scols = intval( 12 / $cols );

        //$btn_classes = isset($btn_style) ? " btn btn-sm btn-inverse btn-block" : "";

        $icon = isset($icon) ? "<style>.wpdm-all-categories li{background: url('{$icon}') left center no-repeat;}</style>" : "";
        $k = 0;
        $html = "
        {$icon}
        <div  class='wpdm-all-categories wpdm-categories-{$cols}col'><div class='row'>";
        foreach($categories as $id => $category){
            $catlink = get_term_link($category);
            if($category->parent == $parent) {

                $count = (isset($showcount) && (int)$showcount == 1) ? "&nbsp;(".$category->count.")" : "";
                $html .= "<div class='col-md-{$scols} cat-div'><a class='wpdm-pcat' href='$catlink' >".htmlspecialchars(stripslashes($category->name)).$count."</a>";

                if(isset($subcat) && $subcat == 1) {
                    $sargs = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'fields' => 'all',
                        'hierarchical' => false,
                        'child_of' => $category->term_id,
                        'pad_counts' => false
                    );
                    $subcategories = get_terms('wpdmcategory', $sargs);
                    $html .= "<div class='wpdm-subcats'>";
                    foreach ($subcategories as $sid => $subcategory) {
                        $scatlink = get_term_link($subcategory);
                        $subcat_count = (isset($showcount) && (int)$showcount == 1) ? "&nbsp;(".$subcategory->count.")" : "";
                        $html .= "<a class='wpdm-scat' href='$scatlink' >" . htmlspecialchars(stripslashes($subcategory->name)) . $subcat_count . "</a>";
                    }
                    $html .= "</div>";
                }
                $html .= "</div>";
                $k++;
            }
        }

        $html .= "</div><div style='clear:both'></div></div>";
        if($k == 0) $html = '';
        return "<div class='w3eden'>".str_replace(array("\r","\n"),"",$html)."</div>";
    }



}