<div class='w3eden'>
    <form id="srcpss" style="margin-bottom:20px">
        <div class="input-group input-group-lg">
            <div class="input-group-addon input-group-append" style="width: 50px;border-radius: 3px 0 0 3px"><span class="input-group-text" id="spro"><i class="fa fa-search"></i></span></div>
            <input type="text" class="form-control input-lg" style="border-radius: 0 3px 3px 0" name="src" value="<?php echo wpdm_query_var('s', 'txt'); ?>" placeholder="<?php _e('Search Package','wpdm-archive-page'); ?>" id="src">
        </div>
    </form>
    <div style='clear: both;'>
        <div  class='wpdm-downloads' id='wpdm-downloads'></div>
    </div>
</div>
<script>
    var wpdmap_params = '<?=\WPDM\__\Crypt::encrypt($params); ?>';
    let _init = <?= (int)$init ?>;
    function htmlEncode(value){
        return jQuery('<div/>').text(value).html();
    }

    jQuery(function ($) {

        $('#srcpss').submit(function(e){
            e.preventDefault();
            /*$('.wpdm-cat-link').removeClass('active');
            $('#inp').html('<?php _e('Search Result For','wpdm-archive-page'); ?> <b>'+htmlEncode($('#src').val())+'</b>');
            $('#spro').html('<i class="fas fa-sun fa-spin color-danger"></i>');
            $.get('<?php the_permalink(); ?>', {action: 'get_downloads', search: encodeURIComponent( $('#src').val()), sc_params: '<?=\WPDM\__\Crypt::encrypt($params); ?>'}, function (response) {
                $('#spro').html('<i class="fa fa-search"></i>');
                $('#wpdm-downloads-ss').html(response.html);
                $('#wpdm-downloads-ss').append(wpdmapPagination(response.current, response.last));
            });*/

        });


        if($('#src').val() !== '')
            $('#srcpss').submit();
    })
</script>
