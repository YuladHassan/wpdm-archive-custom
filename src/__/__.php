<?php


namespace WPDM\AddOn\ArchivePage\__;


use WPDM\__\Crypt;
use WPDM\__\Query;
use WPDM\__\Template;
use WPDM\__\UI;

class __
{

    function __construct()
    {
        add_action("init", [$this, 'getDownloads']);
        //add_action("wp_ajax_get_downloads", [$this, 'getDownloads']);
        //add_action("wp_ajax_nopriv_get_downloads", [$this, 'getDownloads']);

        add_action("wp_ajax_wpdm_change_cat_parent", array( $this, 'setRootCategory'));
        add_action("wp_ajax_nopriv_wpdm_change_cat_parent", array( $this, 'setRootCategory'));
    }

    /**
     * @usage Fetch Packages
     */
    function getDownloads()
    {

        global $wpdb, $current_user;
        if(wpdm_is_ajax() && wpdm_query_var('action') === 'get_downloads') {
            $sparams = Crypt::decrypt(wpdm_query_var('sc_params'), true);
            if (!is_array($sparams)) $sparams = [];
            $params = $sparams;

            $params['template'] = isset($params['template']) && trim($params['template']) !== '' ? $params['template'] : wpdm_valueof($sparams, 'link_template');
            $params['toolbar'] = 0;
            if (!isset($params['paging'])) $params['paging'] = 1;
            if (wpdm_query_var('search') !== '') $params['s'] = wpdm_query_var('search');
            wp_reset_query();
            //Get category from shortcode parameter
            if (isset($params['category'])) {
                $params['categories'] = $params['category'];
            }

            //On the first request, load packages from mentioned featured categories
            if(wpdm_query_var('init', 'int') === 1 && wpdm_valueof($params,'featured_cats') !== '') {
                $params['categories'] = wpdm_valueof($params,'featured_cats');
                unset($params['featured_cats']);
            }

	        if(wpdm_query_var('init', 'int') === 1)
		        $params['include_children'] = 1;

            //Get category from query var, override shortcode parameter
            if (is_array(wpdm_query_var('category'))) {
                $params['categories'] = implode(",", wpdm_query_var('category'));
                $params['operator'] = wpdm_query_var('cat_operator', 'txt');
            } else if (wpdm_query_var('category', 'int') > 0) {
                $term = get_term(wpdm_query_var('category', 'int'));
                $params['categories'] = $term->slug;
                $params['operator'] = wpdm_query_var('cat_operator', 'txt');
            }

            if (is_array(wpdm_query_var('tags'))) {
                $params['tax'] = 'wpdmtag';
                $params['terms'] = implode(",", wpdm_query_var('tags'));
            }

            if(wpdm_query_var('from_date') !== '') {
                $params['from_date'] = wpdm_query_var('from_date');
            }

            if(wpdm_query_var('to_date') !== '') {
                $params['to_date'] = wpdm_query_var('to_date');
            }

            if ($params['template'] === '') $params['template'] = 'link-template-default.php';
            if (!isset($params['cols'])) $params['cols'] = 1;

            $params['toolbar'] = 0;
            wp_send_json($this->packages($params));
            die();
        }
    }

    /**
     * @param array $params
     * @return array
     */
    function packages($params = array('items_per_page' => 10, 'title' => false, 'desc' => false, 'orderby' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => '', 'cols' => 3, 'colspad' => 2, 'colsphone' => 1, 'tags' => '', 'categories' => '', 'year' => '', 'month' => '', 's' => '', 'css_class' => 'wpdm_packages', 'scid' => '', 'async' => 1, 'tax' => '', 'terms' => ''))
    {
        global $current_user, $post;

        static $wpdm_packages = 0;

        if (isset($params['login']) && $params['login'] == 1 && !is_user_logged_in())
            return WPDM()->shortCode->loginForm($params);

        $wpdm_packages++;

        $scparams = $params;

        $defaults = array('author' => '', 'author_name' => '', 'items_per_page' => 10, 'title' => false, 'desc' => false, 'orderby' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => 'link-template-panel', 'cols' => 3, 'colspad' => 2, 'colsphone' => 1, 'css_class' => 'wpdm_packages', 'scid' => 'wpdm_packages_' . $wpdm_packages, 'async' => 1, 'include_children' => 0);
        $params = shortcode_atts($defaults, $params, 'wpdm_packages');

        if (is_array($params))
            extract($params);

        if (!isset($items_per_page) || $items_per_page < 1) $items_per_page = 10;

        $cwd_class = "col-lg-" . (int)(12 / $cols);
        $cwdsm_class = "col-md-" . (int)(12 / $colspad);
        $cwdxs_class = "col-" . (int)(12 / $colsphone);

        if (isset($orderby) && !isset($order_field)) $order_field = $orderby;
        $order_field = isset($order_field) ? $order_field : 'date';
        $order_field = isset($_REQUEST['orderby']) ? esc_attr($_REQUEST['orderby']) : $order_field;
        $order = isset($order) ? $order : 'desc';
        $order = isset($_REQUEST['order']) ? esc_attr($_REQUEST['order']) : $order;
        $cp = wpdm_query_var('cp', 'num');
        if (!$cp) $cp = 1;

        $query = new Query();
        $query->items_per_page(wpdm_valueof($params, 'items_per_page', 10));
        $query->paged($cp);
        $query->sort($order_field, $order);

        foreach ($scparams as $key => $value) {
            if (method_exists($query, $key) && !in_array($key, ['categories', 'tags', 'from_date', 'to_date'])) {
                $query->$key($value);
            }
        }

        /**
         * Process "categories" parameter
         * Usually values are category slug(s), users may use id(s) too
         * If users uses slugs, convert slugs into ids
         **/
        if(wpdm_valueof($scparams, 'categories') !== '') {
            $categories = wpdm_valueof($scparams, 'categories');
            $categories = explode(",", $categories);
            /**
             * Convert slugs to ID
             */
            foreach ($categories as &$cat) {
                if (!is_numeric($cat) && $cat !== '') {
                    $catObj = get_term_by('slug', $cat, 'wpdmcategory');
                    $cat = $catObj->term_id;
                }
            }
            $operator = wpdm_valueof($scparams, 'operator', ['default' => 'IN']);
            if(count($categories) < 2) $operator = 'IN';
            $include_children = wpdm_valueof($scparams, 'include_children', ['default' => false]);
            $query->categories($categories, 'term_id', $operator, $include_children);
        }

        if(wpdm_valueof($scparams, 'from_date') !== '') {
            $query->from_date(wpdm_query_var('from_date'), (wpdm_query_var('date_col') === 'update'));
        }

        if(wpdm_valueof($scparams, 'to_date') !== '') {
            $query->to_date(wpdm_query_var('from_date'), (wpdm_query_var('date_col') === 'update'));
        }

        if(wpdm_valueof($scparams, 'tags') !== '') {
            $tags = wpdm_valueof($scparams, 'tags');
            $operator = wpdm_valueof($scparams, 'operator', ['default' => 'IN']);
            $query->tags($tags, 'slug', $operator);
        }

        if (wpdm_query_var('skw', 'txt') != '') {
            $query->s(wpdm_query_var('skw', 'txt'));
        }

        if(wpdm_valueof($scparams, 'tax') !== '') {
            $_terms = explode("|", wpdm_valueof($scparams, 'terms'));
            $taxos = explode("|", wpdm_valueof($scparams, 'tax'));
            foreach ($taxos as $index => $_taxo) {
                $terms = wpdm_valueof($_terms, $index);
                $terms = explode(",", $terms);
                if(count($terms) > 0) {

                    $query->taxonomy($_taxo, $terms);
                }
            }
        }


        $tax_relation = wpdm_valueof($scparams, 'relation');
        $tax_query = wpdm_valueof($query->params, 'tax_query');
        if(isset($tax_query['relation'])) unset($tax_query['relation']);

        //If multiple taxonomy and tax relation is not mentioned, show intersect result
        if(is_array($tax_query) && count($tax_query) > 1 && !$tax_relation)
            $tax_relation = 'AND';
        else
            $tax_relation = 'OR';

        if($tax_relation)
            $query->taxonomy_relation($tax_relation);

        if (get_option('_wpdm_hide_all', 0) == 1) {
            $query->meta("__wpdm_access", '"guest"');

            if (is_user_logged_in()) {
                foreach ($current_user->roles as $role) {
                    $query->meta("__wpdm_access", $role);
                }
                $query->meta_relation('OR');
            }
        }

        if (isset($scparams['year']) || isset($scparams['month']) || isset($scparams['day'])) {

            if (isset($scparams['day'])) {
                $day = ($scparams['day'] == 'today') ? date('d') : $scparams['day'];
                $query->filter('day', $day);
            }

            if (isset($scparams['month'])) {
                $month = ($scparams['month'] == 'this') ? date('Ym') : $scparams['month'];
                $query->filter('m', $month);
            }

            if (isset($scparams['year'])) {
                $year = ($scparams['year'] == 'this') ? date('Y') : $scparams['year'];
                $query->filter('year', $year);
            }

            if (isset($scparams['week'])) {
                $week = ($scparams['week'] == 'this') ? date('W') : $scparams['week'];
                $query->filter('week', $week);
            }
        }
        //wpdmdd($query->params);
        $query->post_status('publish');
        $query->process();
        $total = $query->count;
        $packages = $query->packages();
		//wpdmdd($packages);

        $pages = ceil($total / $items_per_page);
        $page = isset($_REQUEST['cp']) ? (int)$_REQUEST['cp'] : 1;
        $start = ($page - 1) * $items_per_page;


        $html = '';

        foreach ($packages as $pack){
            $pack = (array)$pack;
            $repeater = "<div class='{$cwd_class} {$cwdsm_class} {$cwdxs_class}'>" . WPDM()->package->fetchTemplate(wpdm_valueof($scparams, 'template', 'link-template-default.php'), $pack) . "</div>";
            $html .= $repeater;

        }

        if($html === '') $html = UI::div(UI::div(esc_attr__( 'No downloads found!', WPDM_TEXT_DOMAIN ), "alert alert-info"), "col-md-12");

        $html = "<div class='row'>{$html}</div>";

        $_scparams = Crypt::encrypt($scparams);

        /*
        $pagination = "";

        global $post;

        $burl = get_permalink();
        $sap = get_option('permalink_structure') ? '?' : '&';
        $burl = $burl . $sap;
        if (isset($_REQUEST['p']) && $_REQUEST['p'] != '') $burl .= 'p=' . esc_attr($_REQUEST['p']) . '&';
        if (isset($_REQUEST['src']) && $_REQUEST['src'] != '') $burl .= 'src=' . esc_attr($_REQUEST['src']) . '&';
        $orderby = isset($_REQUEST['orderby']) ? esc_attr($_REQUEST['orderby']) : 'date';
        $order = ucfirst($order);

        $order_field = " " . __(ucwords(str_replace("_", " ", $order_field)), "wpdmpro");
        $ttitle = __("Title", "download-manager");
        $tdls = __("Downloads", "download-manager");
        $tcdate = __("Publish Date", "download-manager");
        $tudate = __("Update Date", "download-manager");
        $tasc = __("Asc", "download-manager");
        $tdsc = __("Desc", "download-manager");
        $tsrc = __("Search", "download-manager");
        $ord = __("Order", "download-manager");
        $orderby_label = __("Order By", "download-manager");

        $css_class = isset($scparams['css_class']) ? sanitize_text_field($scparams['css_class']) : '';
        $desc = isset($scparams['desc']) ? sanitize_text_field($scparams['desc']) : '';

        $title = isset($title) && $title != '' && $total > 0 ? "<h3>$title</h3>" : "";


        $toolbar = isset($toolbar) ? $toolbar : 0;

        ob_start();
        include Template::locate("shortcodes/packages.php", WPDM_TPL_FALLBACK);
        $content = ob_get_clean();
        $scparams['paging'] = 0;
        $content = WPDM()->package->shortCodes->packages($scparams); //*/
        return ['html' => $html, 'current' => $page, 'last' => $pages, 'params' => $query->params];
    }


    /**
     * @usage Breadcrumb helper, set root category
     */
    function setRootCategory(){
        $cat_id = wpdm_query_var('cat_id', '');
        $result['type'] = 'failed';
        if(is_numeric($cat_id)) {
            $result['type'] = 'success';

            $parents = rtrim($this->getCustomCategoryParents($cat_id,'wpdmcategory',false,'>',false),'>');
            $temp = explode('>', $parents);
            //print_r($temp);
            $count = count($temp);
            $str = "";
            for($i = 1; $i<=$count ; $i++){
                if($i == $count) {
                    $str .= "{$temp[$i-1]}";
                }
                else {

                    $parent = get_term_by('name', $temp[$i-1], 'wpdmcategory');
                    //print_r($parent);
                    $link = get_term_link($parent);
                    //print_r($link);
                    $a = "<a class='wpdm-cat-link2' rel='{$parent->term_id}' test_rel='{$parent->term_id}' title='{$parent->description}' href='$link'>{$parent->name}</a><span class=\"bcsep\"></span>";
                    $str .= $a;
                }
            }
            $result['parent'] = $str;

        }

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();
    }

    /**
     * @param $id
     * @param bool|false $taxonomy
     * @param bool|false $link
     * @param string $separator
     * @param bool|false $nicename
     * @param array $visited
     * @return array|mixed|null|object|string|\WP_Error
     */
    function getCustomCategoryParents( $id, $taxonomy = false, $link = false, $separator = '/', $nicename = false, $visited = array() ) {

        if(!($taxonomy && is_taxonomy_hierarchical( $taxonomy )))
            return '';

        $chain = '';
        // $parent = get_category( $id );
        $parent = get_term( $id, $taxonomy);
        if ( is_wp_error( $parent ) )
            return $parent;

        if ( $nicename )
            $name = $parent->slug;
        else
            $name = $parent->name;

        if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
            $visited[] = $parent->parent;
            // $chain .= get_category_parents( $parent->parent, $link, $separator, $nicename, $visited );
            $chain .= $this->GetCustomCategoryParents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
        }

        if ( $link ) {
            // $chain .= '<a href="' . esc_url( get_category_link( $parent->term_id ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $parent->name ) ) . '">'.$name.'</a>' . $separator;
            $chain .= '<a href="' . esc_url( get_term_link( (int) $parent->term_id, $taxonomy ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s","wpdm-archive-page" ), $parent->name ) ) . '">'.$name.'</a>' . $separator;
        } else {
            $chain .= $name.$separator;
        }
        return $chain;
    }

}
