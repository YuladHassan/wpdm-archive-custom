<div class="w3eden">
    <div class="card wpdmap-header-card" >
        <div class="card-body">
            <?php include WPDM()->template->locate("header.php", 'wpdm-archive-page/filter', __DIR__); ?>
        </div>
        <div class="card-footer">
            <?php include WPDM()->template->locate("breadcrumb.php", 'wpdm-archive-page/filter', __DIR__); ?>
        </div>
    </div>
    <div class="row">
        <?php if(wpdm_valueof($params, 'sidebar') !== 'right' ) { ?>
            <div class="col-md-3 wpdmap-filter-sidebar">
                <?php include WPDM()->template->locate("sidebar{$ex}.php", 'wpdm-archive-page/filter', __DIR__); ?>
            </div>
        <?php } ?>
        <div class="col-md-9">
            <?php include WPDM()->template->locate("content.php", 'wpdm-archive-page/filter', __DIR__); ?>
        </div>
        <?php if(wpdm_valueof($params, 'sidebar') === 'right' ) { ?>
            <div class="col-md-3 wpdmap-filter-sidebar">
                <?php include WPDM()->template->locate("sidebar{$ex}.php", 'wpdm-archive-page/filter', __DIR__); ?>
            </div>
        <?php } ?>

    </div>

</div>

<script>
    var wpdmap_params = '<?= \WPDM\__\Crypt::encrypt($params) ?>';
</script>
        <!-- <script>
                $(document).ready(function() {
                    $('#headingOne<?php echo $cat_id; ?>').on('show.bs.collapse', function() {
                        $(this).find('i').removeClass('fa-plus').addClass('fa-minus');
                    }).on('hide.bs.collapse', function() {
                        $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
                    });
                });
                </script> -->