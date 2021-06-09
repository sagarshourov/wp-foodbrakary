var $ = jQuery;

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

$(document).ready(function ($) {
    if ($(".menu-items-list-holder").length != '') {
        var timesRun = 0;
        setInterval(function () {
            timesRun++;
            if (timesRun === 1) {
                $('.menu-items-list-holder > ul > li > ul').sortable({
                    handle: '.drag-option',
                    cursor: 'move'
                });
				$('.menu-items-list-holder > ul').sortable({
                    handle: '.drag-option',
                    cursor: 'move'
                });
            }
        }, 500);
    }
});

if ($(".restaurant-menu-cats-list").length != '') {
    
    $('.restaurant-menu-cats-list').sortable({
        handle: '.drag-option',
        cursor: 'move'
    });
}

function foodbakery_add_menu_cat(menu_item_counter) {
    jQuery('#add-menu-cat-from-' + menu_item_counter).slideToggle();
}

function foodbakery_close_menu_cat(menu_item_counter) {
    jQuery('#add-menu-cat-from-' + menu_item_counter).slideUp();
}

function foodbakery_add_menu_item(menu_item_counter) {
    jQuery('#add-menu-item-from-' + menu_item_counter).slideToggle();
}

function foodbakery_remove_menu_item(menu_item_counter) {
    jQuery('.menu-item-' + menu_item_counter).remove();
}

function foodbakery_admin_add_menu_cat_to_list(menu_item_counter) {

    var menu_cat_title = $('#menu_item_title_' + menu_item_counter);
    var menu_cat_desc = $('#menu_item_desc_' + menu_item_counter);
    var this_loader = $('#menu-cats-loader-' + menu_item_counter);
    var this_append = $('#restaurant-cats-list-' + menu_item_counter);

    if (menu_cat_title.val() != '') {
        $.ajax({
            url: foodbakery_globals.ajax_url,
            method: "POST",
            data: '_menu_cat_title=' + menu_cat_title.val() + '&_menu_cat_desc=' + menu_cat_desc.val() + '&action=restaurant_add_menu_cat_item',
            dataType: "json"
        }).done(function (response) {
            this_append.append(response.html);
            menu_cat_title.val('');
            menu_cat_desc.val('');
            this_loader.html('');
        }).fail(function () {
            this_loader.html('');
        });
    } else {
        alert('Please fill the required * fields.');
    }
}

function foodbakery_admin_add_menu_item_to_list(restaurant_counter, menu_item_counter, action) {
    var serializedValues = jQuery('#post').serialize();
    var restaurant_menu = jQuery('#restaurant_menu_' + menu_item_counter).val();
    var menu_item_title = jQuery('#menu_item_title_' + menu_item_counter).val();
    var menu_cat_desc = $('#menu_item_desc_' + menu_item_counter);
    var menu_item_price = jQuery('#menu_item_price_' + menu_item_counter).val();
    var menu_item_nutri_res = document.getElementsByName('menu_item_nutri_info[]');
    var menu_item_nutri = [];
    for (var i = 0; i < menu_item_nutri_res.length; i++) {
        menu_item_nutri_res[i].checked ? menu_item_nutri.push(menu_item_nutri_res[i].value) : "";
    }
    var current_restaurant_menu = jQuery('#current_restaurant_menu_' + menu_item_counter).val();

    var valid_price = /^\d{0,6}(\.\d{0,2})?$/.test(menu_item_price);

    if (menu_item_title == 'undefined' || menu_item_title == '' && menu_item_price == 'undefined' || menu_item_price == '') {
        alert(foodbakery_restaurant_strings.compulsory_fields);
        return false;
    } else if (!valid_price) {
        alert(foodbakery_restaurant_strings.valid_price_error);
        return false;
    } else {
        if (action == 'edit' && restaurant_menu == current_restaurant_menu) {
            jQuery('#add-menu-item-from-' + menu_item_counter).slideUp();
        } else {
            jQuery('#menu-item-loader-' + restaurant_counter).html('<div class="loader-holder"><img src="' + foodbakery_globals.plugin_dir_url + 'assets/frontend/images/ajax-loader.gif" alt=""></div>');
            jQuery.ajax({
                type: "POST",
                url: foodbakery_globals.ajax_url,
                dataType: 'json',
                data: serializedValues + '&action=foodbakery_admin_add_menu_item_to_list&restaurant_ad_counter=' + restaurant_counter + '&menu_item_nutri=' + menu_item_nutri + '&menu_item_counter=' + menu_item_counter + '&restaurant_id=' + jQuery('#restaurant_menu_items-list-' + restaurant_counter).data('id') + '&menu_item_add_action=' + action,
                success: function (response) {
                    var parent_list_id = '#restaurant_menu_items-list-' + restaurant_counter;

                    //
                    if (action == 'edit') {
                        var current_restaurant_menu_slug = current_restaurant_menu.replace(' ', '-').toLowerCase();
                        var current_restaurant_menu_slug = current_restaurant_menu_slug.replace('&', '');
                        current_restaurant_menu_slug = current_restaurant_menu_slug.replace(/\//g, "-");
                        var find_current_menu_li = jQuery(parent_list_id + ' li#menu-' + current_restaurant_menu_slug + ' ul.menu-items-list > li').length;
                        if (find_current_menu_li > 1) {
                            jQuery('li.menu-item-' + menu_item_counter).remove();
                        } else {
                            jQuery('li#menu-' + current_restaurant_menu_slug).remove();
                        }
                    }
                    //
                    jQuery('input[name^="menu_item_nutri_info"]').prop('checked', false);
                    jQuery('#add-menu-item-from-' + restaurant_counter).find('.nutri-info-icons').find('li').removeAttr('class');
                    //
                    var restaurants_menu_slug = restaurant_menu.replace(' ', '-').toLowerCase();
                    var restaurants_menu_slug = restaurants_menu_slug.replace('&', '');
                    restaurants_menu_slug = restaurants_menu_slug.replace(/\//g, "-");
                    var find_menu_li = jQuery(parent_list_id + ' li#menu-' + restaurants_menu_slug + '').length;
                    jQuery('#no-menu-items-' + restaurant_counter).remove();
                    if (find_menu_li > 0) {
                        jQuery(parent_list_id + ' li#menu-' + restaurants_menu_slug + ' ul.menu-items-list').append(response.html);
                    } else {
                        jQuery(parent_list_id).append('<li id="menu-' + restaurants_menu_slug + '">\n\
                                <ul class="menu-items-list">\n\
                                    <div class="element-title">\n\
                                        <h5 class="text-color">' + restaurant_menu + '</h5>\n\
                                    </div>\n\
                                    ' + response.html + '\n\
                                </ul>\n\
                          </li>'
                                );
                    }
                    jQuery('#menu-item-loader-' + restaurant_counter).html('');
                    foodbakery_empty_fields_afet_add(restaurant_counter, menu_item_counter, action);
                    foodbakery_show_response(response);
                }
            });
        }
    }
}

function foodbakery_empty_fields_afet_add(restaurant_counter, menu_item_counter, action) {
    if (action == 'add') {
        jQuery('#menu_item_title_' + menu_item_counter).val('');
        jQuery('#menu_item_price_' + menu_item_counter).val('');
        jQuery('#menu_item_desc_' + menu_item_counter).val('');
        jQuery('.menu-item-icon-' + menu_item_counter).val('');
        jQuery('#add-menu-item-from-' + restaurant_counter).slideUp();
        jQuery('#menu-item-extra-list-' + menu_item_counter).empty();
    }
}

function foodbakery_close_menu_item(restaurant_counter, menu_item_counter) {
    jQuery('#add-menu-item-from-' + menu_item_counter).slideUp();
}

function foodbakery_add_menu_item_extra(restaurant_counter, menu_item_counter) {
    var count_extra_li = jQuery('#menu-item-extra-list-' + menu_item_counter + ' > li').length;
    count_extra_inner_li = 0;
    if(count_extra_li > 0){
        
        var totaalBreedte = count_extra_li;
        var menuBreedte = 1;
        var count_extra_inner_li = totaalBreedte - menuBreedte;
    }
   
    jQuery.ajax({
        type: "POST",
        url: foodbakery_globals.ajax_url,
        dataType: 'json',
        data: 'action=foodbakery_admin_restaurant_menu_items_extra_fields&restaurant_ad_counter=' + restaurant_counter + '&menu_item_counter=' + menu_item_counter + '&count_extra_li=' + count_extra_li+ '&count_extra_inner_li=' + count_extra_inner_li,
        success: function (response) {
            jQuery('#menu-item-extra-list-' + menu_item_counter).append(response.html);
            //foodbakery_show_response(response);
        }
    });
}

function add_more_extra_option(restaurant_counter, menu_item_counter, title, price, currency_sign, count_extra_li, count_extra_inner_li) {
    var count_extra_inner_li_next    = parseInt(count_extra_inner_li)+1;//count_extra_inner_li;
    jQuery('#menu-item-extra-fields-' + menu_item_counter + count_extra_li+count_extra_inner_li).closest('.row').append('<ul id="menu-item-extra-fields-' + menu_item_counter + count_extra_li+count_extra_inner_li_next+'" class="menu-item-extra-fields"><li id="menu-item-extra-field-' + menu_item_counter + count_extra_li +count_extra_inner_li_next+ '" class="menu-item-extra-field">\n\
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">\n\
                    <div class="row">\n\
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">\n\
                                    <div class="field-holder">\n\
                                            <label>' + title + '</label>\n\
                                            <input class="menu-item-extra-title" id="menu_item_extra_title_' + menu_item_counter + '" name="menu_item_extra[' + menu_item_counter + '][' + count_extra_li + '][title][]" type="text" value="" placeholder="' + title + '">\n\
                                    </div>\n\
                            </div>\n\
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">\n\
                                    <div class="field-holder">\n\
                                            <label>' + price + ' (' + currency_sign + ')</label>\n\
                                            <input class="menu-item-extra-price" id="menu_item_extra_price_' + menu_item_counter + '" name="menu_item_extra[' + menu_item_counter + '][' + count_extra_li + '][price][]" type="text" value="" placeholder="' + price + '">\n\
                                    </div>\n\
                            </div>\n\
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">\n\
                                    <div class="menu-item-extra-options">\n\
											<label>&nbsp;</label>\n\
                                            <a href="javascript:void(0);" onClick="add_more_extra_option(\'' + restaurant_counter + '\',\'' + menu_item_counter + '\',\'' + title + '\',\'' + price + '\',\'' + currency_sign + '\',\'' + count_extra_li + '\',\'' + count_extra_inner_li_next + '\');">+</a>\n\
                                            <a href="javascript:void(0);" onClick="remove_more_extra_option(\'' + restaurant_counter + '\',\'' + menu_item_counter + '\',\'' + count_extra_li + '\',\'' + count_extra_inner_li_next + '\');">-</a>\n\
                                    </div>\n\
                            </div>\n\
                    </div>\n\
            </div>\n\
      </li></ul>'
            );
}

function remove_more_extra_option(restaurant_counter, menu_item_counter, count_extra_li, count_extra_inner_li) {
    jQuery('#menu-item-extra-field-' + menu_item_counter + count_extra_li + count_extra_inner_li).remove();
}
function remove_more_extra_option_heading(key, menu_item_counter, count_extra_li) {
    jQuery('#extra_li_' + key).remove();
}
function remove_more_extra_option_heading_extra(key, menu_item_counter, count_extra_li) {
   jQuery('.remove_extra_li_'+key).closest("li").remove();	
}

$(document).on("click", ".remove_more_extra_option_heading", function () {
    jQuery(this).parent('.field-holder').parent().remove();
	//alert(heading_li);
	//alert(jQuery('#' + heading_li).parent().attr('class'));
	//jQuery('#' + heading_li).parent().remove();
    //jQuery('#' + heading_li).remove();
});

$(document).on("click", ".nutri-info-icons > ul > li", function () {
    var this_chekbox = $(this).find('input[type="checkbox"]');
    if (this_chekbox.is(':checked')) {
        $(this).addClass('active');
    } else {
        $(this).removeClass('active');
    }
});