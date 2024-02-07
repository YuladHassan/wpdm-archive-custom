<div class="w3eden">

    <div class="row wpdmap-starter">
        <?php include WPDM()->template->locate("sidebar.php", 'wpdm-archive-page/starter', __DIR__); ?>
        <div class="col-md-8">

            <div class="card wpdmap-header-card">
                <div class="card-body">
                    <?php include WPDM()->template->locate("header.php", 'wpdm-archive-page/starter', __DIR__); ?>
                </div>
                <div class="card-footer">
                    <?php include WPDM()->template->locate("breadcrumb.php", 'wpdm-archive-page/starter', __DIR__);  ?>
                </div>
            </div>


            <?php include WPDM()->template->locate("content.php", 'wpdm-archive-page/starter', __DIR__);  ?>

        </div>
    </div>
</div>
<script>
    var wpdmap_params = '<?= \WPDM\__\Crypt::encrypt($params) ?>';
    var wpdmap_default = 1;
    var wpdmap_last_state = <?= (int)$last_state; ?>;
    let _init = <?= (int)wpdm_valueof($params, 'init') ?>;
</script>
