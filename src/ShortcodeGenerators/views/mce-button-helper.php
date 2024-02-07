<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<style>#apc_chosen{ width: 100% !important; } #plnk_tpl_ap_chosen{ width: 140px !important; } #apms_chosen{ width: 200px !important; }</style>
<div class="panel panel-default">
    <div class="panel-heading">Archive Page</div>
    <div class="panel-body">
        <div style="display: inline-block;width: 244px">
            <?php wpdm_dropdown_categories('c',0, 'apc'); ?>
        </div>
        <select style="margin-left: 5px;" id="catvw_ap">
            <option value="extended">Style:</option>
            <option value="hidden">Default</option>
            <option value="compact">Simple</option>
            <option value="extended">Filter</option>
        </select>
        <select style="margin-right: 5px;" id="btns_ap">
            <option value="primary">Button:</option>
            <option value="primary">Primary</option>
            <option value="secondary">Secondary</option>
            <option value="success">Success</option>
            <option value="warning">Warning</option>
            <option value="danger">Danger</option>
            <option value="info">Info</option>
        </select>
        <div style="clear: both;margin-bottom: 5px"></div>

        <?php echo WPDM()->packageTemplate->dropdown(array('id' => 'plnk_tpl_ap', 'class' => 'wpdm-custom-select', 'css' => 'min-width: 298px;'), true); ?>

        <select id="acob" style="margin-right: 5px;width: 100px">
            <option value="post_title">Order By:</option>
            <option value="post_title">Title</option>
            <option value="download_count">Downloads</option>
            <option value="package_size_b">Package Size</option>
            <option value="view_count">Views</option>
            <option value="date">Publish Date</option>
            <option value="modified">Update Date</option>
        </select><select id="acobs" style="margin-right: 5px">
            <option value="asc">Order:</option>
            <option value="asc">Asc</option>
            <option value="desc">Desc</option>
        </select>
        <button class="btn btn-primary" id="acps">Insert to Post</button>
        <script>
            jQuery('#acps').click(function(){

                var cats = jQuery('#apc').val()!='-1'?' category="' + jQuery('#apc').val() + '" ':'';
                var bts = ' button_style="' + jQuery('#btns_ap').val() + '" ';
                var catvw = ' cat_view="' + jQuery('#catvw_ap').val() + '" ';
                var linkt = ' link_template="' + jQuery('#plnk_tpl_ap').val() + '" ';
                var acob = ' orderby="' + jQuery('#acob').val() + '" order="' + jQuery('#acobs').val() + '"';
                var win = window.dialogArguments || opener || parent || top;
                win.send_to_editor('[wpdm-archive' + cats + catvw + bts + linkt + acob + ' items_per_page="10"]');
                tinyMCEPopup.close();
                return false;
            });
        </script>
    </div>
    <div class="panel-heading">Categories</div>
    <div class="panel-body">
        <select id="spc" style="margin-right: 5px">
            <option value="1">Package Count:</option>
            <option value="1">Show</option>
            <option value="0">Hide</option>
        </select><select id="ssc" style="margin-right: 5px">
            <option value="1">Sub Cats:</option>
            <option value="1">Show</option>
            <option value="0">Hide</option>
        </select><select id="apcols" style="margin-right: 5px">
            <option value="3">Cols:</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
        </select>
        <button class="btn btn-primary" id="apcts">Insert to Post</button>
        <script>
            jQuery('#apcts').click(function(){

                var scats =' subcat="' + jQuery('#ssc').val() + '" ';
                var count = ' showcount="' + jQuery('#spc').val() + '" ';
                var cols = ' cols="' + jQuery('#apcols').val() + '" ';
                var win = window.dialogArguments || opener || parent || top;
                win.send_to_editor('[wpdm-categories' + scats + count + cols + ']');
                tinyMCEPopup.close();
                return false;
            });
        </script>
    </div>
    <div class="panel-heading">More...</div>
    <div class="panel-body">
        <select id="apms" style="margin-right: 5px">
            <option value="" disabled="disabled" selected="selected">More Shortcodes...</option>
            <option value='[wpdm-tags cols="4" icon="tag"  btnstyle="default"]'>Tags</option>
            <option value='[wpdm_search_page cols="1" items_per_page="10" link_template="link-template-calltoaction4" position="top"]'>Advanced Search ( Top )</option>
            <option value='[wpdm_search_page cols="1" items_per_page="10" link_template="link-template-calltoaction4" position="left"]'>Advanced Search ( Left )</option>
            <option value='[wpdm_search_page cols="1" items_per_page="10" link_template="link-template-calltoaction4" position="right"]'>Advanced Search ( Right )</option>
        </select>
        <button class="btn btn-primary" id="apmsb">Insert to Post</button>
        <script>
            jQuery('#apmsb').click(function(){
                var win = window.dialogArguments || opener || parent || top;
                win.send_to_editor(jQuery('#apms').val());
                tinyMCEPopup.close();
                return false;
            });
        </script>
    </div>
</div>
