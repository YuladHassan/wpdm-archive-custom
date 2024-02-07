<div class="w3eden">

    <div class="card wpdmap-header-card">
        <div class="card-body">
            <?php include WPDM()->template->locate("header.php", 'wpdm-archive-page/compact', __DIR__); ?>
        </div>
        <div class="card-footer">
            <?php include WPDM()->template->locate("breadcrumb.php", 'wpdm-archive-page/compact', __DIR__); ?>
        </div>
    </div>


    <?php include WPDM()->template->locate("content.php", 'wpdm-archive-page/compact', __DIR__); ?>

</div>
<script>
    var wpdmap_params = '<?= \WPDM\__\Crypt::encrypt($params) ?>';
</script>
