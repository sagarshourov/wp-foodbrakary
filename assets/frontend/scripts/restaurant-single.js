var $ = jQuery;


$(document).on('click', '.menu-category-link', function () {
    var this_id = $(this).data('id');
    var gotom = setInterval(function () {
        restaurant_go_to_navtab(this_id);
        clearInterval(gotom);
    }, 400);
    $('ul.nav-tabs').find('a[href="#home"]').trigger('click');
    $('.menu-list li').removeClass('active');
    $(this).parent().addClass('active');
});

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

function restaurant_go_to_navtab(id) {
    var scrolling_div = $('#menu-category-' + id);
    $('html, body').animate({
        scrollTop: scrolling_div.offset().top - 220
    }, 500);
}

function cs_number_format(num) {
    "use strict";
    return parseFloat(Math.round(num * 100) / 100).toFixed(2);
}

$(document).on('keyup', '.dev-menu-search-field', function () {
    var keyword_val;
    var restaurant_id = $(this).data('id');
    var this_val = $(this).val();
    var main_con = $('#menu-item-list-' + restaurant_id);
    keyword_val = this_val;
    $.ajax({
        url: foodbakery_singles_gl.ajax_url,
        method: "POST",
        data: '_restaurant_id=' + restaurant_id + '&_menu_keyword=' + keyword_val + '&action=restaurant_detail_menu_search',
        dataType: "json"
    }).done(function (response) {
        main_con.html(response.html);
    }).fail(function () {

    });

});

//$(document).on('change', '.dev-menu-orders-list input[name="order_fee_type"]', function () {
$(document).on('change', 'input[name="order_fee_type"]', function () {

    var selected_type_fee = $('input[name="order_fee_type"]:checked').data('fee');
    var selected_type_label = $('input[name="order_fee_type"]:checked').data('label');
    var fee_con = $('.dev-menu-orders-list').find('.restaurant-fee-con');

    fee_con.find('.dev-menu-charges').html(cs_number_format(selected_type_fee));
    fee_con.find('.dev-menu-charges').attr('data-confee', selected_type_fee);
    fee_con.find('.dev-menu-charges').attr('data-fee', selected_type_fee);
    fee_con.find('.fee-title').html(selected_type_label);

    //var subtotal = $('.dev-menu-orders-list').find('.dev-menu-subtotal').text();
    //var charges = $('.dev-menu-orders-list').find('.dev-menu-charges').attr('data-fee');
    //var vtax = $('.dev-menu-orders-list').find('.dev-menu-vtax').html();
    //var total = parseFloat(subtotal) + parseFloat(charges) + parseFloat(vtax);
    //$('.dev-menu-orders-list').find('.dev-menu-grtotal').text(total);

    var _this_type = $('input[name="order_fee_type"]:checked').data('type');
    var _rid = $(this).parents('.dev-select-fee-option').data('rid');
    var ajax_url = foodbakery_singles_gl.ajax_url;
    $.ajax({
        url: ajax_url,
        method: "POST",
        data: 'this_type=' + _this_type + '&_rid=' + _rid + '&action=foodbakery_restaurant_set_fee_type',
        dataType: "json"
    }).done(function (response) {
    }).fail(function () {
    });
    $('.dev-menu-orders-list').trigger('contentchanged');
});

function reset_menu_box(modal_bs) {
    var extras_cons = modal_bs.find('.extras-detail-main');
    if (extras_cons.length !== 0) {
        $.each(extras_cons, function (index, element) {
            if (index == 0) {
                $(this).find('.extras-detail-options').show();
            } else {
                $(this).find('.extras-detail-options').show();
            }
            $(this).find('.extras-detail-selected').html('');
            $(this).find('input[type="radio"]').prop('checked', false);
        });
    }
    // modal_bs.find('button.add-extra-menu-btn').hide();
    //modal_bs.find('.reset-menu-fields').hide();
}

$(document).on('click', '.reset-menu-fields', function () {

    var modal_bs = $(this).parents('.menu-extras-modal');
    $('.extras-detail-options input[type="checkbox"]').prop("checked", false);
    $('.extras-detail-options .extras-detail-att').removeClass("disabled");
    $('.extras-detail-options input[type="checkbox"]').prop("disabled", false);
    jQuery('.required_extras').css('color', '');
    // reseting box
    reset_menu_box(modal_bs);
});

$(document).on('click', '.dev-adding-menu-btn', function () {

    var menu_id = $(this).data('id');
    var menu_cat_id = $(this).data('cid');
    var modal_bs = $('#extras-' + menu_cat_id + '-' + menu_id);
    $('.extras-detail-options input[type="checkbox"]').prop("checked", false);
    var cart_btn = modal_bs.find('button.add-extra-menu-btn');
    cart_btn.html(foodbakery_singles_gl.add_to_menu);
    cart_btn.removeClass('editing-menu');
    cart_btn.removeAttr('data-rand');
    // reseting box
    reset_menu_box(modal_bs);
});

$(document).on('click', '.dev-update-menu-btn', function () {
    var menu_id = $(this).data('id');
    var menu_cat_id = $(this).data('cid');
    var menu_rand = $(this).data('rand');
    var modal_bs = $('#extras-' + menu_cat_id + '-' + menu_id);
    var cart_btn = modal_bs.find('button.add-extra-menu-btn');
    cart_btn.html(foodbakery_singles_gl.update);
    cart_btn.removeClass('editing-menu');
    cart_btn.addClass('editing-menu');
    cart_btn.attr('data-rand', menu_rand);
    // reseting box
    reset_menu_box(modal_bs);
});

$(document).on('click', '.restaurant-add-menu-btn', function () {
    "use strict";
    var selecting_type,
            _this = $(this),
            _this_id = _this.data('id'),
            _this_rid = _this.data('rid'),
            _this_menu_cat_id = _this.data('cid'),
            unique_id = _this.attr('unique_id');
    ajax_url = foodbakery_singles_gl.ajax_url,
            plugin_url = foodbakery_singles_gl.plugin_dir_url,
            this_loader = $('#add-menu-loader-' + _this_id);
    if (!_this.hasClass('is-disabled')) {
        var thisObj = _this;
        foodbakery_show_loader('.restaurant-add-menu-btn-' + _this_id + '', '', 'button_loader', thisObj);
        _this.addClass('is-disabled');
        //this_loader.html('<div class="loader-holder"><img src="' + plugin_url + 'assets/frontend/images/ajax-loader.gif" alt=""></div>');
        selecting_type = $.ajax({
            url: ajax_url,
            method: "POST",
            data: 'menu_cat_id=' + _this_menu_cat_id + '&unique_id=' + unique_id + '&menu_id=' + _this_id + '&_rid=' + _this_rid + '&extra_atts=&action=foodbakery_restaurant_add_menu_item',
            dataType: "json"
        }).done(function (response) {
            //this_loader.html('');
            //foodbakery_show_response(response);
            foodbakery_show_response(response, '', thisObj);
            if (typeof response.li_html !== 'undefined' && response.li_html != '') {
                $('.dev-menu-orders-list').find('.categories-order').append(response.li_html);

                /*Add the stickey cart basket count*/
                var count = $('#item-count').data('count') + 1;
                $('#item-count em').html(count);
                $('#item-count').data('count', count);
                $('#item-count_2').html(count);
                $('.dev-menu-orders-list').show();
                $('#dev-no-menu-orders-list').hide();
                $('.user-order-holder').find('.discount-info').hide();
                $('.user-order-holder').find('.pre-order-msg').hide();
            }
            _this.removeClass('is-disabled');
            $('.dev-menu-orders-list').trigger('contentchanged');
        }).fail(function () {
            //this_loader.html('');
            foodbakery_show_response('', '', thisObj);
            _this.removeClass('is-disabled');
        });
    }
});

$(document).on('click', '.dev-remove-menu-item', function () {
    var selecting_type,
            _this = $(this);
    _this_index = _this.parents('li').index();
    _this_rid = _this.parents('ul').data('rid');
    ajax_url = foodbakery_singles_gl.ajax_url;
    plugin_url = foodbakery_singles_gl.plugin_dir_url;
    this_loader = $(this);

    var quantity = _this.attr('qty');


    var position_arr = [];
    var extra_arr = [];
    var menu_arr = [];


    _this.parents().eq(0).find('li').each(function (index) {
        var position = $(this).attr('position');
        if (position !== '') {
            position_arr.push(position);
            extra_arr.push($(this).attr('extra'));
            menu_arr.push($(this).attr('menu_id'));

        }


    });






    if (!_this.hasClass('is-disabled')) {
        _this.addClass('is-disabled');
        this_loader.html('<img src="' + plugin_url + 'assets/frontend/images/ajax-loader.gif" alt="">');
        selecting_type = $.ajax({
            url: ajax_url,
            method: "POST",
            data: 'menu_id=' + menu_arr.toString() + '&qty=' + quantity + '&extra=' + extra_arr.toString() + '&position=' + position_arr.toString() + '&_id=' + _this_index + '&_rid=' + _this_rid + '&action=foodbakery_restaurant_remove_menu_item',
            //dataType: "json"
        }).done(function (response) {
            this_loader.html('<i class=" icon-cross3"></i>');
            _this.removeClass('is-disabled');

            if (_this.parents('ul').find('> li').length === 1) {
                $('.dev-menu-orders-list').hide();
                $('#dev-no-menu-orders-list').show();
                $('.user-order-holder').find('.discount-info').show();
                $('.user-order-holder').find('.pre-order-msg').show();
            }

            var remove_item_class = _this.parents('li').attr('class');
            jQuery('.' + remove_item_class).remove();

            $('.dev-menu-orders-list').trigger('contentchanged');
            /*remove the stickey cart basket count*/
            var count = $('#item-count').data('count') - 1;
            $('#item-count em').html(count);
            $('#item-count').data('count', count);
            $('#item-count_2').html(count);
        }).fail(function () {
            this_loader.html('<i class=" icon-cross3"></i>');
            _this.removeClass('is-disabled');
        });
    }
});

jQuery(document).on('change', ".extras-detail-att input[type='checkbox']", function () {
    var thisObj = jQuery(this);
    var total_required = thisObj.closest('.extras-detail-main').find("input[name='required_count']").val();
    var checked_values = thisObj.closest('.extras-detail-main').find("input[type='checkbox']:checked").length;
    if (checked_values >= total_required) {
        thisObj.closest('.extras-detail-main').find("input[type='checkbox']").not(':checked').each(function () {
            //jQuery(this).closest('.extras-detail-att').addClass('disabled');
        });
        // thisObj.closest('.extras-detail-main').find("input[type='checkbox']").not(':checked').attr("disabled", true);
    } else {
        thisObj.closest('.extras-detail-main').find("input[type='checkbox']").not(':checked').each(function () {
            jQuery(this).closest('.extras-detail-att').removeClass('disabled');
        });
        thisObj.closest('.extras-detail-main').find("input[type='checkbox']").not(':checked').attr("disabled", false);
    }
});


$(document).on('click', '.sa_show_content', function () {

    var these = $(this);

    var extra_option = these.next();

    extra_option.show();

    // alert('hi');

});





$(document).on('click', '.sa_increment', function () {
    var thisObj = jQuery(this);

    var parent = thisObj.parent();

    var sa_quantity = parent.find('.sa_quantity_in');

    var sa_quantity_val = parseInt(sa_quantity.val());

    var sa_checkbox = thisObj.parents().eq(6).find('.sa_extra_checkbox');

    var total_quantity = thisObj.parents().eq(6).find('.total_quantity');


    sa_quantity_val = sa_quantity_val + 1;

    var total_count = parseInt(thisObj.parents().eq(6).find('.total_count').attr('total_count'));

    var flag = true;


    sa_checkbox.each(function (index, e) {

        var these = $(this);

        var title = these.attr('title');
        var max_qty = these.attr('max_qty');
        var ids = these.attr('id');

        if (these.is(':checked')) {
            var total_qty = 0;
            $(document).find('.sa_quantity_info').each(function () {
                var dat = $(this).attr('dat');
                if ((dat !== undefined) && ids === dat) {
                    total_qty = total_qty + Number($(this).attr('qty'));

                }
            });










            if (max_qty !== '') {
                max_qty = parseInt(max_qty);

                var sub_quntity = max_qty - total_qty;


                total_qty = total_qty + sa_quantity_val;
                if (total_qty > max_qty) {
                    alert('Only ' + sub_quntity + ' ' + title + ' Products are available');
                    flag = false;
                    return;

                }
            }
        }



    });



    if (flag) {

        sa_quantity.val(sa_quantity_val);

        parent.find('.sa_quantity').text(sa_quantity_val);

        total_quantity.text(sa_quantity_val);

        var price = calculatePrice(thisObj) * sa_quantity_val;

        thisObj.parents().eq(6).find('.total_count').attr('total_count', price.toFixed(2));
        thisObj.parents().eq(6).find('.total_count').text('Total : ' + price.toFixed(2) + ' €');

    }




    // $(document).find('.sa_extra_checkbox').trigger("click");    


});

function calculatePrice(thisObj) {

    var sa_checkbox = thisObj.parents().eq(6).find('.sa_extra_checkbox');
    var price = 0.0;

    sa_checkbox.each(function (inex) {
        var these = $(this);
        if (these.is(':checked')) {

            price = price + parseFloat(these.attr('price'));



        }


    });



    return price;





}


$(document).on('click', '.sa_decrement', function () {
    var thisObj = jQuery(this);
    var parent = thisObj.parent();
    var total_quantity = thisObj.parents().eq(6).find('.total_quantity');
    var sa_quantity = parent.find('.sa_quantity_in');
    var sa_quantity_val = parseInt(sa_quantity.val());
    sa_quantity_val = sa_quantity_val - 1;


    if (sa_quantity_val > -1) {

        var total_count = parseInt(thisObj.parents().eq(6).find('.total_count').attr('total_count'));


        sa_quantity.val(sa_quantity_val);

        parent.find('.sa_quantity').text(sa_quantity_val);

        total_quantity.text(sa_quantity_val);



        var price = calculatePrice(thisObj) * sa_quantity_val;


        thisObj.parents().eq(6).find('.total_count').attr('total_count', price.toFixed(2));
        thisObj.parents().eq(6).find('.total_count').text('Total : ' + price.toFixed(2) + ' €');



    }



});







$(document).on('click', '.add-extra-menu-btn', function () {
    thisObj = jQuery(this);

    jQuery('.required_extras').css('color', '');

    //console.log(thisObj.parents().eq(5).attr('id'));

    var _this = $(this),
            _this_menu_cat_id = _this.data('menucat-id'),
            unique_id = _this.attr('unique_id');
    _this_menu_cat_id_new = _this.data('menucat-id-new'),
            _this_menu_id = _this.data('menu-id'),
            _this_menu_unique_id = _this.data('unique-menu-id'),
            _this_rid = _this.data('rid'),
            // main_con = jQuery('#extras-' + _this_menu_cat_id + '-' + _this_menu_id),
            main_con = thisObj.parents().eq(7),
            add_cart_btn = main_con.find('button.add-extra-menu-btn'),
            ajax_url = foodbakery_singles_gl.ajax_url,
            plugin_url = foodbakery_singles_gl.plugin_dir_url,
            this_loader = _this.next('span');
    var is_updating = 'false';
    var rand_numb = 0;
    var menu_index = 0;
    var notes = '';
    var sa_quantity = $(this).parents().eq(7).find('.sa_quantity_in').val();

    console.log(sa_quantity);

    if (_this.hasClass('editing-menu')) {

        is_updating = 'true';
        rand_numb = _this.attr('data-rand');
        menu_index = $('#menu-added-' + rand_numb).index();
        //  main_con = $('#edit_extras-' + _this_menu_cat_id_new + '-' + _this_menu_id);

        main_con = thisObj.parents().eq(7);
    }


    var extras_con = main_con.find('.extras-detail-main');
    var total_extras = extras_con.length;


    var extra_notes = main_con.find('input[name="extrasnotes"]').val();



    if (total_extras > 0) {
        var extra_check_f = true;
        var sa_quantity_check = true;
        var extra_name_arr = [];
        $.each(extras_con, function (index, element) {

            var required_count = Number($(this).find('input[name="required_count"]').val());
            var field_required = $(this).find('input[name="field_required"]').val();
            var extras_detail_att = $(this).find('.extras-detail-att');
            var this_extra_n = index + '-' + _this_menu_id;
            extra_name_arr.push(this_extra_n);
            var checked_attr = 0;
            $.each(extras_detail_att, function (index_x, element) {
                var this_extr = $(this).find('input');
                if (this_extr.is(":checked")) {
                    checked_attr = Number(checked_attr) + 1;


                    var ids = this_extr.attr('id');

                    var max_qty = this_extr.attr('max_qty');
                    var title = this_extr.attr('title');


                    if (max_qty === '') {
                        sa_quantity_check = true;
                    } else {

                        var total_qty = 0;

                        $(document).find('.sa_quantity_info').each(function () {

                            var dat = $(this).attr('dat');

                            if ((dat !== undefined) && ids === dat) {
                                total_qty = total_qty + Number($(this).attr('qty'));

                            }
                        });


                        var sub_quentity = max_qty - total_qty;
                        total_qty = total_qty + Number(sa_quantity);



                        if (total_qty > max_qty) {
                            //extra_check_f = false;

                            alert('Only' + sub_quentity + ' ' + title + 'avaiable');
                            sa_quantity_check = false;

                        }
                    }






                    //categories-order
                }




            });

            if (field_required == 'Yes' && checked_attr == 0) {
                extra_check_f = false;
                $(this).find('.required_extras').css('color', 'red');
            }

            // console.log('required_count ' + required_count);

            if (required_count > 0) {
                if (checked_attr !== required_count) {
                    extra_check_f = false;
                    $(this).find('.required_extras').css('color', 'red');
                }
            }

            //console.log('checked_attr ' + checked_attr);


        });

        if (false === extra_check_f) {
            alert(foodbakery_singles_gl.select_menu_items);
        } else if (false === sa_quantity_check) {
            this_loader.html('');
            _this.removeClass('is-disabled');
        } else {
            foodbakery_show_loader('.add-extra-menu-btn', '', 'button_loader', thisObj);
            var extra_index = '';
            var extra_labels_arr = [];
            var rand_number = jQuery(this).data('rand');
            $.each(extra_name_arr, function (index, element) {
                var this_extr_name = main_con.find('input[name="extra-' + element + '"]:checked');
                extra_index = this_extr_name.data('ind');
                extra_labels_arr.push(extra_index);
            });
            if (!_this.hasClass('is-disabled')) {
                _this.addClass('is-disabled');
                this_loader.html('<img src="' + plugin_url + 'assets/frontend/images/ajax-loader.gif" alt="">');
                extra_labels_arr = extra_labels_arr.toString();
                /*Extra Attributes Jquery*/
                var extra_atts_ary = [];
                var extra_tittle_ary = [];
                var sa_quantity_ary = [];
                jQuery.each(jQuery('.extras-detail-options input[type="checkbox"]:checked'), function (index, e) {
                    var ind = $(e).data('ind');
                    var ex_tittle = $(e).attr('name');
                    extra_atts_ary.push(ind);
                    ex_tittle = ex_tittle.split('-');
                    extra_tittle_ary.push(ex_tittle[1]);
                });
                jQuery.each(jQuery('.extras-detail-options input[type="radio"]:checked'), function (index, e) {
                    var ind = $(e).data('ind');
                    var ex_tittle = $(e).attr('name');
                    extra_atts_ary.push(ind);
                    ex_tittle = ex_tittle.split('-');
                    extra_tittle_ary.push(ex_tittle[1]);
                });
                var extra_atts = extra_atts_ary.toString();
                var extra_name = extra_tittle_ary.toString();

                jQuery.each(jQuery('.sa_quantity_in'), function (index, e) {

                    var values = $(this).val();

                    if (values > 0) {
                        sa_quantity_ary.push(values);
                    }


                });




                $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: 'rand_number =' + rand_number + '&menu_unique_id =' + _this_menu_unique_id + '&unique_id=' + unique_id + '&menu_cat_id=' + _this_menu_cat_id + '&menu_id=' + _this_menu_id + '&_rid=' + _this_rid + '&extra_name=' + extra_name + '&extra_atts=' + extra_atts + '&sa_quantity=' + sa_quantity + '&act_updating=' + is_updating + '&menu_index=' + menu_index + '&extra_notes=' + extra_notes + '&action=foodbakery_restaurant_add_menu_item',
                    dataType: "json"
                }).done(function (response) {
                    this_loader.html('');
                    foodbakery_show_response(response, '', thisObj);
                    if (typeof response.li_html !== 'undefined' && response.li_html != '') {
                        if ('true' == is_updating) {
                            $('.menu-added-' + rand_numb).after(response.li_html);
                            $('.menu-added-' + rand_numb).remove();
                        } else {
                            $('.dev-menu-orders-list').find('.categories-order').append(response.li_html);
                            //$('.dev-menu-orders-list > ul').append(response.li_html);
                            $('.dev-menu-orders-list').show();
                            $('#dev-no-menu-orders-list').hide();
                        }
                    }
                    _this.removeClass('is-disabled');
                    $.each(extra_name_arr, function (index, element) {
                        var this_extr_name = main_con.find('input[name="extra-' + element + '"]:checked');
                        this_extr_name.prop('checked', false);
                    });
                    add_cart_btn.show();
                    main_con.modal('toggle');
                    $('.dev-menu-orders-list').trigger('contentchanged');

                }).fail(function () {
                    this_loader.html('');
                    _this.removeClass('is-disabled');
                });
            }
        }
    }

});

$(document).on('click', '.sa_extra_checkbox', function () {
    var total_count = 0.0;
    var these = $(this);
    var quantity = parseInt(these.closest('.modal-content').find('.sa_quantity_in').val());
    var model = these.parents().eq(8);
    var quantity = parseInt(model.find('.sa_quantity_in').val());
    setTimeout(function () {
        $(".up.extras-detail-main.condition_div.contcheckopts").each(function () {
            if ($(this).css("display") == "block") {
                var r_val = $(this).find("input[name='required_count']").data("required");
                $(this).find("input[name='required_count']").val(r_val);
            }
        });
    }, 500);
    model.find('.sa_extra_checkbox').each(function () {
        var theses = $(this);
        var title = theses.attr('title');

        if (theses.is(":checked")) {
            var max_qty = theses.attr('max_qty');
            var ids = theses.attr('id');
            if (max_qty === "") {

            } else {

                var total_qty = 0;

                $(document).find('.sa_quantity_info').each(function () {

                    var dat = $(this).attr('dat');

                    if ((dat !== undefined) && ids === dat) {
                        total_qty = total_qty + Number($(this).attr('qty'));

                    }
                });


                var sub_quentity = max_qty - total_qty;
                total_qty = total_qty + Number(quantity);

                if (total_qty > Number(max_qty)) {
                    alert('Only ' + sub_quentity + ' ' + title + ' Products are available');
                    //theses.trigger('click');

                    // theses.checked = 0;

                    these.removeAttr('checked');

                }










            }


            var price = parseFloat(theses.attr('price'));
            total_count = total_count + price;
        }

    });


    var quantitys = quantity * total_count;

    var new_price = these.closest('.modal-content').find('.total_count');



    new_price.text('Total : ' + parseFloat(quantitys).toFixed(2) + ' €');

    //console.log(total_count);



});

$(document).on('click', '.extras-detail-att input[type="checkbox"]', function () {
    var _this = $(this),
            _this_menu_id = _this.data('menu-id'),
            main_con = _this.parents('.menu-selection-container'),
            add_cart_btn = main_con.find('button.add-extra-menu-btn'),
            reset_fields_btn = main_con.find('a.reset-menu-fields');
    var extras_con = main_con.find('.extras-detail-main');
    var total_extras = extras_con.length;
    //_this.parents('.extras-detail-options').hide();
    //var inject_selected = '<i class="icon-check2"></i> <span>' + (_this.parents('label').find('.extra-title').html() + ' - ' + _this.parents('label').find('.extra-price').html()) + '</span>';
    //_this.parents('.extras-detail-main').find('.extras-detail-selected').append(inject_selected);
    //_this.parents('.extras-detail-main').next('.extras-detail-main').find('.extras-detail-options').show();
    if (total_extras > 0) {
        var extra_check_f = true;
        $.each(extras_con, function (index, element) {
            var this_extr = $(this).find('input[name="extra-' + index + '-' + _this_menu_id + '"]');
            if (!this_extr.is(":checked")) {
                extra_check_f = false;
            }
        });
    }
    if (true === extra_check_f) {
        add_cart_btn.show();
        reset_fields_btn.show();
    } else {
        add_cart_btn.show();
        reset_fields_btn.show();
    }
});

$(document).on('contentchanged', '.dev-menu-orders-list', function () {
    var subtotal = 0;
    var subtotal_noc = 0;
    var subtotal_con = $('.dev-menu-orders-list').find('.dev-menu-subtotal');
    var main_con = $('.dev-menu-orders-list:first');

    var main_menus = main_con.find('> ul > li');

    $.each(main_menus, function (index, element) {
        var this_pr = $(this).attr('data-conpr');

        subtotal += parseFloat(this_pr);

        var this_pr_noc = $(this).attr('data-pr');
        subtotal_noc += parseFloat(this_pr_noc);

    });

    subtotal_con.html(cs_number_format(subtotal));
    $('#order_subtotal_price').val(cs_number_format(subtotal_noc));

    var menu_fee_pr = $('.dev-menu-orders-list').find('.dev-menu-charges').attr('data-confee');
    var menu_fee_pr_noc = $('.dev-menu-orders-list').find('.dev-menu-charges').attr('data-fee');
    if (parseFloat(menu_fee_pr) > 0) {
        subtotal += parseFloat(menu_fee_pr);
    }

    if (parseFloat(menu_fee_pr_noc) > 0) {
        subtotal_noc += parseFloat(menu_fee_pr_noc);
    }


    var vat_switch = $('.dev-menu-orders-list').find('.dev-menu-price-con').attr('data-vatsw');
    var vat_perc = $('.dev-menu-orders-list').find('.dev-menu-price-con').attr('data-vat');

    var vat_price = 0;
    if (vat_switch == 'on' && subtotal > 0) {
        vat_price = (subtotal / 100) * (parseFloat(vat_perc));
        $('.dev-menu-orders-list').find('.dev-menu-vtax').html(cs_number_format(vat_price));
        $('#order_vat_cal_price').val(cs_number_format(vat_price));
    }

    subtotal += vat_price;

    if (vat_switch == 'on' && subtotal_noc > 0) {
        var vat_price_noc = (subtotal_noc / 100) * (parseFloat(vat_perc));
        $('#order_vat_cal_price').val(cs_number_format(vat_price_noc));
    }
    subtotal_noc += vat_price_noc;

    $('.dev-menu-orders-list').find('.dev-menu-grtotal').html(cs_number_format(subtotal));

    var cartcount = jQuery("#count_cart_items").children('.categories-order').children('li').length;
    jQuery('.cart-count').html(cartcount);
    if (cartcount == 0) {
        jQuery('.dev-no-menu-items-list').show();
        jQuery('#cart-total-price').html(cs_number_format(0));
    } else {
        jQuery('#cart-total-price').html(cs_number_format(subtotal));
    }

});


function calcDistance(p1, p2) {

    var distence = (google.maps.geometry.spherical.computeDistanceBetween(p1, p2) / 1000).toFixed(2);
    //  console.log(distence);
    return distence;
}


$(document).on('click', '#sa_cupon_apply', function () {
    var in_value = $('#in_coupon').val();
    var old_price = parseFloat($('.dev-menu-grtotal').text());
    if (in_value !== '') {
        $(this).parents().eq(1).removeClass('has-error ');
        $(this).parents().eq(1).addClass('has-success');
        $('#helpBlockError').hide();
        $('#helpBlockSuccess').show();

        $('.dev-menu-grtotal').text((old_price - 2));
        $('#sa_applied_coupon').show();
    } else {

        $('#helpBlockError').show();
        $('#helpBlockSuccess').hide();
        if ($(this).parents().eq(1).hasClass('has-success')) {
            $('#sa_applied_coupon').hide();
            $('.dev-menu-grtotal').text((old_price + 2));
        }
        $(this).parents().eq(1).removeClass('has-success');
        $(this).parents().eq(1).addClass('has-error');
    }

});



$(document).on('click', '.menu-order-confirm', function () {
    var _this = $(this),
            ajax_url = foodbakery_singles_gl.ajax_url,
            plugin_url = foodbakery_singles_gl.plugin_dir_url,
            this_loader = $('.menu-loader');
    thisObj = jQuery(this);

    // thisObj.css('position', 'relative');
    // foodbakery_show_loader('.menu-order-confirm', '', 'button_loader', thisObj);

    var btn_text = $('.menu-order-confirm').html();
    $('.menu-order-confirm').html(btn_text + ' <div class="foodbakery-button-loader" style="display: block;"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>');
    $('.menu-order-confirm').css('position', 'relative');
    $('.menu-order-confirm').addClass('foodbakery-processing');

    var rid = _this.data('rid');
    var pay_method = '';
    var order_subtotal_price = '';
    var order_vat_percent = '';
    var order_vat_cal_price = '';
    var delivery_date = '';
    var user_order_tip = '';
    var user_delivery_address = '';
    var order_fee_type = '';



    var user_lat = parseFloat($("#sa_data").attr('user_lat'));
    var user_lng = parseFloat($("#sa_data").attr('user_lag'));

    var rest_lag = parseFloat($("#sa_data").attr('resturent_lag'));
    var rest_lat = parseFloat($("#sa_data").attr('resturent_lat'));

    var distance = 0.0;
    var dist = $("#sa_data").attr('distance');

    //console.log(user_lat);

    if (user_lat === 0) {
        //console.log('_________lat_____');
        $('.login-popup').trigger('click');
        $('.foodbakery-button-loader').remove();
        $('.menu-order-confirm').removeAttr('style');
        $('.menu-order-confirm').removeClass('foodbakery-processing');
        return;
    }

    if (dist === undefined) {
        // console.log('______________');


        $('.login-popup').trigger('click');


        $('.foodbakery-button-loader').remove();
        $('.menu-order-confirm').removeAttr('style');
        $('.menu-order-confirm').removeClass('foodbakery-processing');
        return;
    }

    distance = parseFloat($("#sa_data").attr('distance'));


    var p1 = new google.maps.LatLng(user_lat, user_lng);
    var p2 = new google.maps.LatLng(rest_lat, rest_lag);



    if ($('.dev-order-pay-options').find('input[name="order_payment_method"]').length !== 0) {
        pay_method = $('.dev-order-pay-options').find('input[name="order_payment_method"]:checked').data('type');
    }
    if ($('#order_subtotal_price').length !== 0) {
        order_subtotal_price = $('#order_subtotal_price').val();
    }
    if ($('#order_vat_percent').length !== 0) {
        order_vat_percent = $('#order_vat_percent').val();
    }
    if ($('#order_vat_cal_price').length !== 0) {
        order_vat_cal_price = $('#order_vat_cal_price').val();
    }
    if ($('input[name="delivery_date"]').length !== 0) {
        delivery_date = $('input[name="delivery_date"]').val();
    }
    if ($('input[name="user_area_zipcode"]').length !== 0) {
        user_area_zipcode = $('input[name="user_area_zipcode"]').val();
    }
    if ($('#user_order_tip').length !== 0) {
        user_order_tip = $('#user_order_tip').val();
    }
    if ($('#user_delivery_address').length !== 0) {
        user_delivery_address = $('#user_delivery_address').val();
    }
    if ($("input[name='order_fee_type']").length > 0) {
        var order_fee_type = $("input[name='order_fee_type']:checked").val();
    }


//console.log($("input[name='order_fee_type']:checked").val());

    if ($("input[name='order_fee_type']:checked").val() === 'pickup') {


    } else {
        if (calcDistance(p1, p2) > distance) {

            alert(` Η διεύθυνση που έχετε προσθέσει ή είναι λάθος ή είναι εκτός της εμβέλιας περιοχής της διανομής κατ' οίκον" Παρακαλούμε ελέγξε το στις ρυθμίσεις λογαριασμού:
            https://delivery.food-fellas.gr/user-dashboard/?dashboard=account`);




            $('.foodbakery-button-loader').remove();
            $('.menu-order-confirm').removeAttr('style');
            $('.menu-order-confirm').removeClass('foodbakery-processing');
            return;

        }




    }





    if (!_this.hasClass('is-disabled')) {
        _this.addClass('is-disabled');
        //this_loader.html('<img src="' + plugin_url + 'assets/frontend/images/ajax-loader.gif" alt="">');
        $.ajax({
            url: ajax_url,
            method: "POST",
            data: 'order_fee_type=' + order_fee_type + '&user_delivery_address=' + user_delivery_address + '&user_order_tip=' + user_order_tip + '&_pay_method=' + pay_method + '&_rid=' + rid + '&order_subtotal_price=' + order_subtotal_price + '&order_vat_percent=' + order_vat_percent + '&order_vat_cal_price=' + order_vat_cal_price + '&delivery_date=' + delivery_date + '&action=foodbakery_restaurant_order_confirm',
            dataType: "json"
        }).done(function (response) {

            // console.log(response);
            if (response.is_user_login == false) {
                jQuery('#sign-in').modal('show');
                jQuery('.wp-user-form').append('<input type="hidden" name="single_restaurant_page" class="single_restaurant_page" value="YES">');
            }
            if (response.type == 'redirect') {
                this_loader.html(response.message);
            } else {
                this_loader.html('');
            }
            if (response.type == 'success' && response.pay_method == 'cash') {
                $('.dev-menu-orders-list').find('> ul > li').remove();
                $('.dev-menu-orders-list').hide();
                $('#dev-no-menu-orders-list').show();
            }
            foodbakery_show_response(response, '', thisObj);
            _this.removeClass('is-disabled');

            /* Remove loader */
            $('.foodbakery-button-loader').remove();
            $('.menu-order-confirm').removeAttr('style');
            $('.menu-order-confirm').removeClass('foodbakery-processing');

        }).fail(function () {
            this_loader.html('');
            _this.removeClass('is-disabled');
        });
    }

});

var is_device = function () {
    "use strict";
    return {
        init: function () {
            var isMobile = {
                Android: function () {
                    return navigator.userAgent.match(/Android/i);
                },
                BlackBerry: function () {
                    return navigator.userAgent.match(/BlackBerry/i);
                },
                iOS: function () {
                    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
                },
                Opera: function () {
                    return navigator.userAgent.match(/Opera Mini/i);
                },
                Windows: function () {
                    return navigator.userAgent.match(/IEMobile/i);
                },
                any: function () {
                    return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
                }
            };
            if (isMobile.any()) {

            } else {
                // scroll to top disable for mobile Start
                /*if ($(window).width() > 767) {
                 jQuery(document).on('shown.bs.tab', '.stickynav-tabs a[data-toggle="tab"]', function (e) {
                 jQuery("html, body").animate({
                 scrollTop: 0
                 }, 'slow');
                 });
                 }*/
                // scroll to top disable for mobile End
            }
        }
    };

}();

$(document).ready(function () {
    "use strict";
    is_device.init(); // init core 
    if (jQuery(".contact-area .phone > a").length > 0) {
        var mm = jQuery(".contact-area .phone > a").data("original-title");
        mm = mm.split(":")[1].trim();
    }

    if (jQuery(".contact-area .phone > a").length > 0) {
        jQuery(".contact-area .phone > a").attr("href", "tel:+30" + mm);
        jQuery(".cs-calltoaction.simple a.csborder-color.cs-color").attr("href", "#home");
        jQuery(".cs-calltoaction.simple a.csborder-color.cs-color").click(function () {
            var desti = jQuery(this).attr("href");
            jQuery('html, body').animate({
                scrollTop: jQuery(desti).offset().top - 50
            }, 2000);
        });
    }
});
function foodbakery_edit_extra_menu_item(popup_id, data_id, data_cat_id, data_rand, ajax_url, restaurant_id, unique_id, menu_unique_id, extra_child_menu_id) {

    plugin_url = foodbakery_singles_gl.plugin_dir_url;
    jQuery('.update_menu_' + data_rand).hide();
    jQuery('.update_menu_' + data_rand).after('<img class="update_menu_loader_' + data_rand + '" src="' + plugin_url + 'assets/frontend/images/ajax-loader.gif" alt="">');

    var modal_popup_id = popup_id;
    jQuery('#show_extra_modal').html('');

    jQuery.ajax({
        type: "POST",
        url: ajax_url,
        dataType: 'json',
        data: 'action=foodbakery_edit_extra_menu_item&popup_id=' + popup_id + '&data_id=' + data_id + '&data_cat_id=' + data_cat_id + '&data_rand=' + data_rand + '&restuarant_id=' + restaurant_id + '&unique_id=' + unique_id + '&menu_unique_id=' + menu_unique_id + '&extra_child_menu_id=' + extra_child_menu_id,
        success: function (response) {
            if (response.status == 'success') {
                jQuery('#edit_extra_modal').html(response.html)
                jQuery('#' + modal_popup_id).modal('show');
            }
            foodbakery_show_response(response);
            jQuery('.update_menu_loader_' + data_rand).remove();
            jQuery('.update_menu_' + data_rand).show();
        }
    });

}
function foodbakery_show_extra_menu_item(popup_id, data_id, data_cat_id, ajax_url, restaurant_id) {
    var modal_popup_id = popup_id;


    "use strict";

    var selecting_type,
            _this = $(this),
            this_loader = $('#add-menu-loader-' + data_id);
    if (!_this.hasClass('is-disabled')) {
        var thisObj = _this;
        //foodbakery_show_loader('.dev-adding-menu-btn-' + data_id + '', '', 'button_loader', thisObj);
        _this.addClass('is-disabled');
    }
    jQuery.ajax({
        type: "POST",
        url: ajax_url,
        dataType: 'json',
        data: 'action=foodbakery_show_extra_menu_item&popup_id=' + popup_id + '&data_id=' + data_id + '&data_cat_id=' + data_cat_id + '&restaurant_id=' + restaurant_id,
        success: function (response) {
            if (response.status == 'success') {

                jQuery('#show_extra_modal').html(response.html);
                jQuery('#' + modal_popup_id).modal('show');
            }
            foodbakery_show_response(response);

        }
    });
}