<?php


namespace WPDM\AddOn\ArchivePage\Templates\Tags;


class Tags
{
    function __construct()
    {
        add_shortcode("wpdm_tags", [$this, 'render']);
    }

    function render($params = [])
    {
        global $wpdb;
        @extract($params);
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
            'parent'         => 0,
            'hierarchical'  => false,
            'child_of'      => 0,
            'get'           => '',
            'name__like'    => '',
            'pad_counts'    => false,
            'offset'        => '',
            'search'        => '',
            'cache_domain'  => 'core'
        );
        $tags = get_terms('wpdmtag',$args);
        $pluginsurl = plugins_url();
        $cols = isset($cols)&&$cols>0?$cols:2;
        $scols = intval(12/$cols);
        $icon = isset($icon)?"<i class='fa fa-{$icon}'></i>":"<i class='fa fa-tag'></i>";
        $btnstyle = isset($btnstyle)?$btnstyle:'success';
        $k = 0;
        $html = "<div  class='wpdm-all-categories wpdm-categories-{$cols}col'><ul class='row'>";
        foreach($tags as $id => $tags){
            $catlink = get_term_link($tags);
            $ccount = $tags->count;
            if(isset($showcount)&&$showcount) $count  = "&nbsp;<span class='wpdm-count'>($ccount)</span>";
            $html .= "<div class='col-md-{$scols} col-tag'><a class='btn btn-{$btnstyle} btn-block text-left' href='$catlink' >{$icon} &nbsp; ".htmlspecialchars(stripslashes($tags->name))."</a></div>";
            $k++;
        }

        $html .= "</ul><div style='clear:both'></div></div><style>.col-tag{ margin-bottom: 10px !important; } .col-tag .btn{ text-align: left !important; padding-left: 10px !important; box-shadow: none !important; }</style>";
        if($k==0) $html = '';
        return "<div class='w3eden'>".str_replace(array("\r","\n"),"",$html)."</div>";
    }



}