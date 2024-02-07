<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div class="card wpdmap-card-filter mb-3">
    <div class="card-header">
        <?=__('Categories', 'wpdm-archive-page'); ?>
    </div>
    <div class="card-header p-2 filter-header bg-white"><input placeholder="<?php echo __( "Search...", "download-manager" ) ?>" type="search" class="form-control form-control-sm bg-white input-sm" id="cat_src" /></div>
    <div class="card-body cat-card">
            <?php
            WPDM()->categories->checkboxTree('cats', [], ['class' => 'wpdmap-cat-filter', 'value' => 'slug', 'parent' => $parent, 'hide_parent' => 1, 'categories' => $categories]);
            ?>
    </div>
    <div class="card-footer">
        <?= esc_attr__('Match', 'wpdm-archive-page'); ?> <label class="mr-3 ml-3"><input type="radio" value="IN" class="operator" name="operator"><?= esc_attr__('Any', 'wpdm-archive-page'); ?> </label><label><input type="radio" class="operator" value="AND" name="operator"><?= esc_attr__('All', 'wpdm-archive-page'); ?></label>
    </div>
</div>
<div class="card wpdmap-card-filter mb-3">
    <div class="card-header">
        <label class="d-block">
            <span class="float-right pull-right"><input id="dates_filter" type="checkbox"></span>
            <?=__('Filter Date Range', 'wpdm-archive-page'); ?>
        </label>
    </div>
    <div class="card-body">
        <div class="form-group m-0">
            <label class="mr-3"><input checked="checked" class="dates_column" type="radio" name="date" value="publish"> <?=__('Published', 'wpdm-archive-page'); ?></label>
            <label><input type="radio" class="dates_column" name="date" value="update"> <?=__('Updated', 'wpdm-archive-page'); ?></label>
        </div>
        <input class="form-control" id="dates" type="text" name="dates" />
    </div>
</div>
<div class="card wpdmap-card-filter mb-3">
    <div class="card-header">
        <?=__('Tags', 'wpdm-archive-page'); ?>
    </div>
    <div class="card-header p-2 filter-header bg-white"><input placeholder="<?php echo __( "Search...", "download-manager" ) ?>" type="search" class="form-control form-control-sm bg-white input-sm" id="tag_src" /></div>
    <div class="card-body tag-card">
        <ul  id="wpdm-tags">
            <?php
            $tax_name = version_compare(WPDM_VERSION, '5.0.0', '>') ? 'wpdmtag' : 'post_tag';
            $terms = get_terms(['taxonomy' => $tax_name, 'hide_empty' => false]);
            foreach($terms as $term){
                if(is_object($term))
                    echo "<li class='wpdm-tag'><label><input type='checkbox' name='wpdmtags[]' class='wpdmtag' value='{$term->slug}'> <span class='tagname'>{$term->name}</span></label></li>";
            }

            ?>
        </ul>
    </div>
</div>

<!-- div class="card wpdmap-card-filter mb-3">
    <div class="card-header">
        <?=__('Download Count', 'wpdm-archive-page'); ?> [ >= ]
    </div>
    <div class="card-body">
        <input type="range" class="form-control" name="download_count" />
    </div>
</div>
<div class="card wpdmap-card-filter mb-3">
    <div class="card-header">
        <?=__('View Count', 'wpdm-archive-page'); ?> [ >= ]
    </div>
    <div class="card-body">
        <input type="range" class="form-control" name="view_count" />
    </div>
</div -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<?php
$loop = get_posts( 'numberposts=1&order=ASC&post_type=wpdmpro' );
$first = $loop[0]->post_date;
$now = date("Y-m-d");
?>
<script>
    var wpdmap_sdate = moment("<?=$first?>").format("YYYY-MM-DD"), wpdmap_edate = moment("<?=$now?>").format("YYYY-MM-DD");
    jQuery(function ($){
        $('.wpdmap-filter-sidebar input[name="dates"]').daterangepicker(
            {
                opens: 'right',
                startDate: moment("<?=$first?>"),
                endDate: moment("<?=$now?>"),
                locale: {
                    format: '<?= wpdm_valueof($params, 'date_format', 'DD/MM/YYYY'); ?>'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }
        );

    });
</script>
