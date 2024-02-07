<?php
$post = (array)get_post($_POST['pid']);
$terms = wp_get_post_terms( $_POST['pid'], 'wpdmcategory' );
$template = isset($_POST['pagetemplate']) && $_POST['pagetemplate'] != ''?$_POST['pagetemplate']:get_post_meta($_POST['pid'], '__wpdm_page_template', true);

echo "<div class='breadcrumb' style='border-radius:0;'>";
if(!$parent)
    \WPDM\Category\CategoryController::CategoryBreadcrumb($terms[0]->term_id,1);
else
    echo "<a href='#' class='folder' data-cat='{$parent}'>".esc_attr__( 'Home', WPDMAP_TEXT_DOMAIN )."</a>";
echo "</div>";
echo FetchTemplate($template, $post, 'page');
