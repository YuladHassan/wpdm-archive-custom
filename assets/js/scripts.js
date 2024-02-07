const { __, _x, _n, sprintf } = wp.i18n;
var wpdmap_dcol = '';
function wpdmapPagination(current, last) {
    var delta = 2,
        left = current - delta,
        right = current + delta + 1,
        range = [],
        rangeWithDots = [],
        l;

    for (let i = 1; i <= last; i++) {
        if (i === 1 || i === last || i >= left && i < right) {
            range.push(i);
        }
    }

    for (let i of range) {
        if (l) {
            if (i - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (i - l !== 1) {
                rangeWithDots.push('...');
            }
        }
        rangeWithDots.push(i);
        l = i;
    }
    var html = "<ul class='pagination wpdm-pagination pagination-centered text-center'>";
    console.log(current);
    if(current > 1)
        html += "<li><a href='#' data-page='"+(current-1)+"' class='async_page page-numbers'><i style=\"display: inline-block;width: 8px;height: 8px;border-right: 1px solid;border-top: 1px solid;transform: rotate(-135deg);margin-left: -2px;margin-top: 2px;\"></i></a></li>"
    for(i = 0; i < rangeWithDots.length; i++){
        var cclass = parseInt(rangeWithDots[i]) === current ? 'current-page' : '';
        if(rangeWithDots[i] !== '...')
            html += "<li><a href='#' data-page='"+rangeWithDots[i]+"' class='async_page page-numbers "+cclass+"'>"+rangeWithDots[i]+"</a></li>";
        else
            html += "<li><a class='page-numbers dot'>"+rangeWithDots[i]+"</a></li>";
    }
    if(current < last)
        html += "<li><a href='#' data-page='"+(current+1)+"' class='async_page page-numbers'><i style=\"display: inline-block;width: 8px;height: 8px;border-right: 1px solid;border-top: 1px solid;transform: rotate(45deg);margin-left: -2px;margin-top: -2px;\"></i></a></li>"
    html += "</ul>";
    return "<div class='text-center'>"+html+"</div>";
}


jQuery(function ($) {

    function htmlEncode(value) {
        return $('<div/>').text(value).html();
    }

    var wpdmac_category = '', wpdmac_tags = '';

    var __wpdmap_loaded_cat = localStorage.getItem('__wpdmap_loaded_cat');
    if (location.href.split('#').length == 2)
        __wpdmap_loaded_cat = (location.href.split('#')[1]).replace(/\//ig, "_");
    if (!__wpdmap_loaded_cat || __wpdmap_loaded_cat === 'undefined') __wpdmap_loaded_cat = '';

    function expandMenu(menu) {
        $(menu).addClass('active').removeClass('collapsed').attr('aria-expanded', true).find('.fa').removeClass('fa-chevron-down').addClass('fa fa-chevron-up');
        $($(menu).attr('href')).addClass('show');
    }

    function autoExpand() {
        /*var ids = localStorage.getItem('__wpdmap_open_cat');
        if(ids) {
            ids = ids.split('/');
            $.each(ids, function (index, id) {
                //$('#clspt-' + id).trigger('click');
                $('#clspt-' + id).addClass('active').removeClass('collapsed').attr('aria-expanded', true).find('.fa').removeClass('fa-chevron-down').addClass('fa fa-chevron-up');
                $('#collapse-' + id).addClass('show');
            });
        }*/

        $('.btn-clps').each(function () {
            var open = localStorage.getItem($(this).attr('id'));
            if (parseInt(open) === 1) {
                //$(this).trigger('click');
                expandMenu(this);
            }

        });

        if (__wpdmap_loaded_cat) {
            //$('#wpdm-cat-link-'+__wpdmap_loaded_cat).trigger('click');
            var cat = $('#wpdm-cat-link-' + __wpdmap_loaded_cat);
            if (cat) {
                cat.addClass('active');
                var cat_id = cat.attr('rel');
                wpdmac_category = cat_id;
                getDownloads(1);
                setParentCat(cat_id);
                if (cat.data('path') && cat.data('path') !== undefined)
                    window.location.hash = "#" + cat.data('path');
            }
        }
    }

    function _wpdmap_last_state() {
        return (typeof wpdmap_last_state === 'undefined') ? 0 : wpdmap_last_state;
    }

    function getDownloads(cp, container, init) {

        WPDM.blockUI('#wpdm-archive-page');
        var scode_params = typeof wpdmap_params !== 'undefined' ? wpdmap_params : '';
        jQuery('#wpdm-downloads').prepend('<div class="wpdm-loading">' + wpdm_js.spinner + ' ' + __('Loading', 'wpdm-archive-pae') + '...</div>');
        var from_date = '';
        var to_date = ''
        if (jQuery('#dates_filter').is(':checked')) {
            from_date = wpdmap_sdate;
            to_date = wpdmap_edate;
        }
        init = typeof init === 'undefined' ? 0 : 1;
        jQuery.post(wpdm_url.home, {
            action: 'get_downloads',
            cp: cp,
            init: init,
            search: jQuery('#src').val(),
            category: wpdmac_category,
            cat_operator: jQuery('.operator:checked').val(),
            tags: wpdmac_tags,
            orderby: jQuery('#orderby').val(),
            order: jQuery('#order').val(),
            from_date: from_date,
            to_date: to_date,
            date_col: wpdmap_dcol,
            sc_params: scode_params
        }, function (response) {
            if (typeof container === 'undefined') container = '#wpdm-downloads';
            jQuery(container).html(response.html + wpdmapPagination(cp, response.last));
            WPDM.unblockUI('#wpdm-archive-page');
        });
    }

    function setParentCat(cat_id) {
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: wpdm_url.ajax,
            data: {action: "wpdm_change_cat_parent", cat_id: cat_id},
            success: function (response) {
                console.log(response);
                if (response.type === "success") {
                    if (jQuery('#src').val() !== '')
                        jQuery('#inp').html(__('Search Result For', 'wpdm-archive-pae') + ' <b>' + htmlEncode(jQuery('#src').val()) + '</b> ' + __('in category', 'wpdm-archive-pae') + ' <b>' + response.parent + '</b>');
                    else
                        jQuery('#inp').html(response.parent);
                }
            }
        });
    }

    function selectedCats() {
        var _items = [];
        $('#wpdmcat-tree input[type=checkbox]').each(function (index, item) {
            if ($(this).is(":checked")) _items.push($(this).val());
        });
        return _items;
    }

    function selectedTags() {
        var _items = [];
        $('#wpdm-tags input[type=checkbox]').each(function (index, item) {
            if ($(this).is(":checked")) _items.push($(this).val());
        });
        return _items;
    }

    var $body = $('body');

    /*$body.on('click', '.pagination a', function (e) {
        e.preventDefault();
        $('#wpdm-downloads').prepend('<div class="wpdm-loading">'+wpdm_js.spinner+' '+__('Loading', 'wpdm-archive-pae')+'...</div>').load(this.href);
    });*/


    $('.wpdm-cat-link').click(function (e) {
        e.preventDefault();
        $('.wpdm-cat-link').removeClass('active').remo;
        $(this).addClass('active');
        var cat_id = $(this).attr('rel');
        wpdmac_category = cat_id;
        setParentCat(cat_id);
        getDownloads(1);
        if ($(this).data('path') !== undefined && _wpdmap_last_state()) {
            window.location.hash = "#" + $(this).data('path');
            localStorage.setItem("__wpdmap_loaded_cat", $(this).data('path').replace(/\//ig, "_"));
        }
        $('#clspt-' + cat_id).attr('aria-expanded', "true").addClass('active').removeClass('collapsed');
        $('#clspt-' + cat_id).find('i.fa').addClass('fa-chevron-up').removeClass('fa-chevron-down');
        if (typeof $().collapse === 'undefined')
            $('#collapse-' + cat_id).slideDown();
        else
            $('#collapse-' + cat_id).collapse('show')

        /*$('#clspt-' + cat_id).addClass('active').removeClass('collapsed').attr('aria-expanded', true).find('.fa').removeClass('fa-chevron-down').addClass('fa fa-chevron-up');
        $('#collapse-' + cat_id).addClass('show');*/
    });

    $('#wpdm-cats-compact').on('change', function (e) {
        var cat_id = $(this).val();
        if (cat_id == -1) cat_id = 0;
        $('#initc').val(cat_id);
        var sfparams = $('#srcp').serialize()
        wpdmac_category = cat_id;
        setParentCat(cat_id);
        getDownloads(1);
    });


    $('.wpdmap-filter-sidebar input[name="dates"]').on('apply.daterangepicker', function (ev, picker) {
        wpdmap_sdate = picker.startDate.format('YYYY-MM-DD');
        wpdmap_edate = picker.endDate.format('YYYY-MM-DD');
        wpdmac_category = selectedCats();
        wpdmac_tags = selectedTags();
        $('#initc').val(wpdmac_category);
        var sfparams = $('#srcp').serialize();
        getDownloads(1);
    });

    $('.dates_column').on('change', function () {
        wpdmap_dcol = $(this).val();
    });

    $('#wpdmcat-tree input[type=checkbox], #wpdm-tags input[type=checkbox], .dates_column, #dates_filter, .operator').on('change', function (e) {
        wpdmac_category = selectedCats();
        wpdmac_tags = selectedTags();
        $('#initc').val(wpdmac_category);
        var sfparams = $('#srcp').serialize()
        getDownloads(1);
    });

    $('#wpdmcat-tree .cat_filter').on('click', function (e) {
        e.preventDefault();
        wpdmac_category = [$(this).data('value')];
        wpdmac_tags = selectedTags();
        $('#initc').val(wpdmac_category);
        var sfparams = $('#srcp').serialize()
        getDownloads(1);
    });

    $body.on('click', '.async_page', function (e) {
        e.preventDefault();
        e.stopPropagation();
        getDownloads($(this).data('page'));
        return false;
    });

    $body.on('click', '.wpdm-cat-link2', function (e) {

        e.preventDefault();
        $('.wpdm-cat-link').removeClass('active');
        var new_rel = $(this).attr('test_rel');
        if (new_rel !== 'undefined') {
            $('a[rel=' + new_rel + ']').addClass('active');
        }

        var cat_id = jQuery(this).attr('rel');
        wpdmac_category = cat_id;
        setParentCat(cat_id);
        getDownloads(1);

    });

    $body.on('click', '.w3eden a.btn-clps[data-toggle="collapse"]', function (e) {
        e.preventDefault();
        var status = $(this).children('.fa').attr('class'); //fa fa-chevron-up
        $(this).children('.fa').toggleClass('fa-chevron-down');
        $(this).children('.fa').toggleClass('fa-chevron-up');
        $(this).toggleClass('active');
        localStorage.setItem($(this).attr('id'), 1);
        var current = localStorage.getItem('__wpdmap_open_cat');
        var expand = $(this).data('parents');
        if (status === 'fa fa-chevron-up') {
            localStorage.setItem("__wpdmap_open_cat", '');
            localStorage.setItem($(this).attr('id'), 0);
        } else {
            localStorage.setItem("__wpdmap_open_cat", $(this).data('parents'));
            localStorage.setItem($(this).attr('id'), 1);
        }
        if (typeof $().modal === 'undefined')
            $($(this).attr('href')).slideToggle();

        /**
         setTimeout(function() {
         var __wpdmap_loaded_cat = localStorage.getItem('__wpdmap_loaded_cat');
         __wpdmap_loaded_cat = __wpdmap_loaded_cat.replace(/_/ig, "/");
         if(!__wpdmap_loaded_cat || __wpdmap_loaded_cat === undefined) __wpdmap_loaded_cat = "";
         location.href = "#" + __wpdmap_loaded_cat;
         }, 10);
         */
    });

    $body.on('click', '#reset-filter', function (e) {
        e.preventDefault();
        $('#wpdmcat-tree input[type=checkbox], #wpdm-tags input[type=checkbox]').removeAttr('checked');
        $('#src').val('');
        $('#srcp').resetForm();
        wpdmac_category = '';
        wpdmac_tags = '';
        getDownloads(1);
    });

    $('#orderby, #order').on('change', function () {
        getDownloads(1);
    });


    $('#srcpss').submit(function (e) {
        e.preventDefault();
        getDownloads(1);
    });

    $('#srcp').submit(function (e) {
        e.preventDefault();
        $('.wpdm-cat-link').removeClass('active');
        $('#inp').html(__('Search Result For', 'wpdm-archive-pae') + ' <b>' + htmlEncode($('#src').val()) + '</b>');

        getDownloads(1);

    });

    $('#wpdm-archive-page-home').click(function (e) {
        e.preventDefault();
        $('.wpdm-cat-link').removeClass('active');
        $('#inp').html(__('All Downloads', 'wpdm-archive-pae'));
        wpdmac_category = '';
        $('#src').val('');
        getDownloads(1);
    });

    $body.on("keyup", "#cat_src", function () {
        var value = $(this).val().toLowerCase();
        $("#wpdmcat-tree li").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    $body.on("keyup", "#tag_src", function () {
        var value = $(this).val().toLowerCase();
        $("#wpdm-tags .wpdm-tag").filter(function () {
            console.log($(this).text());
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });


    var autox = 0;
    if (typeof wpdmap_last_state !== 'undefined' && wpdmap_last_state === 1) {
        autoExpand();
        autox = 1;
    }

    let __init_gd = 1;
    if (typeof _init !== "undefined")
        __init_gd = _init;


    if ($('#wpdm-downloads').length > 0 && autox === 0 && __init_gd === 1) {
        getDownloads(1, '#wpdm-downloads', 1);
    }


        $(document).ready(function() {
        $('.toggle-icon').click(function () {
            $(this).find('i').toggleClass('fa-plus fa-minus');
        });
    });


});
