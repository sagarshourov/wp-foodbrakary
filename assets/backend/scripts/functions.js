/*
 * hide page section
 */


var $ = jQuery;

    "use strict";
    if ($("#cs-pb-formelements").length != '') {
        $('#cs-pb-formelements').sortable();
    }

    if ($("#cs-pb-formelements_form_builder").length != '') {
        $('#cs-pb-formelements_form_builder').sortable();
    }

    /*
     * Media Upload 
     */
    var contheight;
    jQuery(document).on("click", ".uploadMedia, .cs-uploadMedia", function () {
        var $ = jQuery;
        var id = $(this).attr("name");
        var custom_uploader = wp.media({
            title: 'Select File',
            button: {
                text: 'Add File'
            },
            multiple: false
        })
                .on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    jQuery('#' + id).val(attachment.id);
                    jQuery('#' + id + '_img').attr('src', attachment.url);
                    jQuery('#' + id + '_box').show();
                }).open();

    });



    /*
     * Update Team Member
     */
    jQuery(document).on('click', '#team_update_form', function () {
        var user_id = jQuery(this).closest('form').data('id');
        foodbakery_show_loader();
        var serializedValues = jQuery("#foodbakery_update_team_member" + user_id).serialize();
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: foodbakery_globals.ajax_url,
            data: serializedValues + '&foodbakery_user_id=' + user_id + '&action=foodbakery_update_team_member',
            success: function (response) {
                jQuery('#foodbakery_publisher_company').trigger('click');
                foodbakery_show_response(response);
            }


        });
    });


    jQuery(document).on('click', '.changeicon', function () {
        jQuery('#foodbakery_map_t_op_search').click();

    });

    /*
     * Remove Team Member
     */
    jQuery(document).on('click', '.remove_member', function () {
        var thisObj = jQuery(this);
        var user_id = thisObj.closest('form').data('id');
        var count_supper_admin = thisObj.closest('form').data('count_supper_admin');
        var selected_user_type = thisObj.closest('form').data('selected_user_type');
        foodbakery_show_loader();
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: foodbakery_globals.ajax_url,
            data: 'foodbakery_user_id=' + user_id + '&selected_user_type=' + selected_user_type + '&count_supper_admin=' + count_supper_admin + '&action=foodbakery_remove_team_member',
            success: function (response) {
                if (response.type == 'success') {
                    jQuery('#foodbakery_publisher_company').trigger('click');
                    thisObj.closest('form').fadeOut('slow');

                }
                foodbakery_show_response(response);
            }
        });
    });




//load role related profile fields
    jQuery(document).on("change", "#role", function () {
        var selected_role = jQuery(this).find(":selected").val();
        if (selected_role == 'foodbakery_candidate') {
            //jQuery("#employer_tab").hide();
            // jQuery("#candidate_tab").show();
            jQuery(".foodbakery-user-customfield-block").show();
            jQuery(".foodbakery-employer-fields").hide();
            jQuery(".foodbakery-candidate-fields").show();
        } else if (selected_role == 'foodbakery_publisher') {
            jQuery(".foodbakery-user-customfield-block").show();
            jQuery(".foodbakery-candidate-fields").hide();
            jQuery(".foodbakery-employer-fields").show();
            // jQuery("#candidate_tab").hide();
            // jQuery("#employer_tab").show();
        } else {
            jQuery(".foodbakery-user-customfield-block").hide();
            jQuery(".foodbakery-employer-fields").hide();
            jQuery(".foodbakery-candidate-fields").hide();
            //  jQuery("#candidate_tab").hide();
            // jQuery("#employer_tab").hide();
        }

    });



    /*
     * hide page section
     */

    var myUrl = window.location.href; //get URL
    var myUrlTab = myUrl.substring(myUrl.indexOf("#")); // For localhost/tabs.html#tab2, myUrlTab = #tab2     
    var myUrlTabName = myUrlTab.substring(0, 4); // For the above example, myUrlTabName = #tab
    jQuery("#tabbed-content > div").addClass('hidden-tab'); // Initially hide all content #####EDITED#####
    jQuery("#foodbakery-options-tab li:first a").attr("id", "current"); // Activate first tab
    jQuery("#tabbed-content > div:first").hide().removeClass('hidden-tab').fadeIn(); // Show first tab content   #####EDITED#####
    jQuery("#foodbakery-options-tab > li:first").addClass('active');

    jQuery(document).on("click", "#foodbakery-options-tab li > a", function (e) {
        e.preventDefault();
        if (jQuery(this).attr("id") == "current") { //detection for current tab
            return
        } else {
            foodbakery_reset_tabs();
            jQuery("#foodbakery-options-tab > li").removeClass('active');
            jQuery(this).attr("id", "current"); // Activate this
            jQuery(this).parents('li').addClass('active');
            jQuery(jQuery(this).attr('name')).hide().removeClass('hidden-tab').fadeIn(); // Show content for current tab
        }
    });

    var i;
    for (i = 1; i <= jQuery("#foodbakery-options-tab li").length; i++) {
        if (myUrlTab == myUrlTabName + i) {
            foodbakery_reset_tabs();
            jQuery("a[name='" + myUrlTab + "']").attr("id", "current"); // Activate url tab
            jQuery(myUrlTab).hide().removeClass('hidden-tab').fadeIn(); // Show url tab content        
        }
    }


    // End here
    jQuery(document).on('click', '#wrapper_boxed_layoutoptions1', function (event) {

        var theme_option_layout = jQuery('#wrapper_boxed_layoutoptions1 input[name=layout_option]:checked').val();
        if (theme_option_layout == 'wrapper_boxed') {
            jQuery("#layout-background-theme-options").show();
        } else {
            jQuery("#layout-background-theme-options").hide();
        }
    });
    jQuery(document).on('click', '#wrapper_boxed_layoutoptions2', function (event) {
        var theme_option_layout = jQuery('#wrapper_boxed_layoutoptions2 input[name=layout_option]:checked').val();
        if (theme_option_layout == 'wrapper_boxed') {
            jQuery("#layout-background-theme-options").show();
        } else {
            jQuery("#layout-background-theme-options").hide();

        }

    });
    /*
     * textarea header_code_indent
     */

    jQuery('textarea.header_code_indent').keydown(function (e) {
        if (e.keyCode == 9) {
            var start = $(this).get(0).selectionStart;
            $(this).val($(this).val().substring(0, start) + "    " + $(this).val().substring($(this).get(0).selectionEnd));
            $(this).get(0).selectionStart = $(this).get(0).selectionEnd = start + 4;
            return false;
        }
    });
    /*
     * Toggle Function
     */

    jQuery(".hidediv").hide();
    jQuery(document).on('click', '.showdiv', function (event) {
        jQuery(this).parents("article").stop().find(".hidediv").toggle(300);
    });

    chosen_selectionbox();

// Membership detail Click
$(document).on('click', '.foodbakery-dev-dash-detail-pkg', function () {
    var _this_id = $(this).data('id'),
            package_detail_sec = $('#package-detail-' + _this_id);

    if (!package_detail_sec.is(':visible')) {
        $('.all-pckgs-sec').find('.package-info-sec').hide();
        package_detail_sec.slideDown();
    } else {
        package_detail_sec.slideUp();
    }

});

function foodbakery_reset_tabs() {
    "use strict";
    jQuery("#tabbed-content > div").addClass('hidden-tab'); //Hide all content
    jQuery("#foodbakery-options-tab a").attr("id", ""); //Reset id's      
}
jQuery(document).on('click', '.user_gallery li.image.ui-sortable-handle', function () {

    var attachment_id = $(this).attr('data-attachment_id');
    var image_url = $(this).children('img').attr('src');
    $('#foodbakery_profile_image_box .thumb-secs img').attr('src', image_url);
    $('#foodbakery_profile_image').val(attachment_id);
});
jQuery(document).on('click', 'label.foodbakery-chekbox', function () {
    var checkbox = jQuery(this).find('input[type=checkbox]');

    if (checkbox.is(":checked")) {
        jQuery('#' + checkbox.attr('name')).val(checkbox.val());
        jQuery('#' + checkbox.attr('name')).attr('value', 'on');
    } else {
        jQuery('#' + checkbox.attr('name')).val('off');
        jQuery('#' + checkbox.attr('name')).attr('value', 'off');
    }
});

/*
 * upload file url
 */

jQuery(document).on('click', 'uploadfileurl', function () {
    var $ = jQuery;
    var id = $(this).attr("name");
    var custom_uploader = wp.media({
        title: 'Select File',
        button: {
            text: 'Add File'
        },
        multiple: false
    })
            .on('select', function () {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                jQuery('#' + id).val(attachment.url);
            }).open();

});
/*
 * 
 *  number of featured restaurants check
 */
jQuery("#foodbakery_package_fieldnumber_of_featured_restaurantsvalue, #foodbakery_package_fieldnumber_of_top_cat_restaurantsvalue").keyup(function () {
    var val = jQuery(this).val();
    var error_message = jQuery(this).attr('data-error');
    var number_of_allowed_restaurants = jQuery('#foodbakery_package_fieldnumber_of_restaurant_allowedvalue').val();
    if (parseInt(val) > parseInt(number_of_allowed_restaurants)) {
        alert(error_message);
        jQuery(this).val('');

    }
    var val = '';

});

/**
 * Plugin Option Saving
 *
 */
function plugin_option_save(admin_url) {
    returnType = foodbakery_validation_process(jQuery("#plugin-options"));
    if (returnType == false) {
        return false;
    }
    "use strict";
    jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);
    // enable disabled select fields before storing data
    var disabled = $("#plugin-options select:disabled").prop('disabled', false);

    function newValues() {
        var serializedValues = jQuery("#plugin-options input,#plugin-options select,#plugin-options textarea").serialize() + '&action=plugin_option_save';
        return serializedValues;
    }
    var serializedReturn = newValues();
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: serializedReturn,
        success: function (response) {

            jQuery(".loading_div").hide();
            jQuery(".form-msg .innermsg").html(response);
            jQuery(".form-msg").show();
            jQuery(".outerwrapp-layer").delay(3000).fadeOut(500);
            slideout();
            // Disable disabled selects back again.
            disabled.prop('disabled', true);
        }
    });
}


/**
 * Plugin Reset Option
 *
 */
function cs_rest_plugin_options(admin_url) {
    "use strict";

    var var_confirm = confirm("You current Plugin options will be replaced with the default options.");
    if (var_confirm == true) {
        var dataString = 'action=plugin_option_rest_all';
        jQuery.ajax({
            type: "POST",
            url: admin_url,
            data: dataString,
            success: function (response) {

                jQuery(".form-msg").show();
                jQuery(".form-msg").html(response);
                jQuery(".loading_div").hide();
                window.location.reload(true);
                slideout();
            }
        });
    }
}

/*
 * reset tabs
 */
function resetTabs() {
    jQuery("#tabbed-content > div").addClass('hidden-tab'); //Hide all content
    jQuery("#foodbakery-options-tab a").attr("id", ""); //Reset id's      
}
/*
 * del media
 */
function del_media(id) {
    var $ = jQuery;
    jQuery('input[name="' + id + '"]').show();
    jQuery('#' + id + '_box').hide();
    jQuery('#' + id).val('');
    jQuery('#' + id).next().show();
}
/*
 * toggle with value
 */
function toggle_with_value(id) {
    if (id == 0) {
        jQuery("#wrapper_repeat_event").hide();
    } else {
        jQuery("#wrapper_repeat_event").show();
    }
}

/*
 * chosen selection box
 */

function chosen_selectionbox() {
    if (jQuery('.chosen-select, .chosen-select-deselect, .chosen-select-no-single, .chosen-select-no-results, .chosen-select-width').length != '') {
        var config = {
            '.chosen-select': {width: "100%"},
            '.chosen-select-deselect': {allow_single_deselect: true},
            '.chosen-select-no-single': {disable_search_threshold: 4, width: "100%"},
            '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chosen-select-width': {width: "95%"}
        }
        for (var selector in config) {
            jQuery(selector).chosen(config[selector]);
        }
    }
}

/*
 * gllsearch map
 */
function gll_search_map() {
    var vals;
    vals = jQuery('#foodbakery_location_address').val();
    jQuery('.gllpSearchField').val(vals);
}
/*
 * remove image
 */
function remove_image(id) {
    var $ = jQuery;
    $('#' + id).val('');
    $('#' + id + '_img_div').hide();
    //$('#'+id+'_div').attr('src', '');
}
/*
 * slideout
 */
function slideout() {
    setTimeout(function () {
        jQuery(".form-msg").slideUp("slow", function () {
        });
    }, 5000);
}
/*
 * div remove
 */
function foodbakery_div_remove(id) {
    jQuery("#" + id).remove();
}
/*
 * foodbakery_toggle
 */
function foodbakery_toggle(id) {
    jQuery("#" + id).slideToggle("slow");
}
/*
 * foodbakery_toggle_height
 */
function foodbakery_toggle_height(value, id) {
    var $ = jQuery;
    if (value == "Post Slider") {
        jQuery("#post_slider" + id).show();
        jQuery("#choose_slider" + id).hide();
        jQuery("#layer_slider" + id).hide();
        jQuery("#show_post" + id).show();
    } else if (value == "Flex Slider") {
        jQuery("#choose_slider" + id).show();
        jQuery("#layer_slider" + id).hide();
        jQuery("#post_slider" + id).hide();
        jQuery("#show_post" + id).hide();
    } else if (value == "Custom Slider") {
        jQuery("#layer_slider" + id).show();
        jQuery("#choose_slider" + id).hide();
        jQuery("#post_slider" + id).hide();
        jQuery("#show_post" + id).hide();
    } else {
        jQuery("#" + id).removeClass("no-display");
        jQuery("#post_slider" + id).show();
        jQuery("#choose_slider" + id).hide();
        jQuery("#layer_slider" + id).hide();
        jQuery("#show_post" + id).hide();
    }
}
/*
 * foodbakery_toggle_list
 */
function foodbakery_toggle_list(value, id) {
    var $ = jQuery;

    if (value == "custom_icon") {
        jQuery("#" + id).addClass("no-display");
        jQuery("#foodbakery_list_icon").show();
    } else {
        jQuery("#" + id).removeClass("no-display");
        jQuery("#foodbakery_list_icon").hide();
    }
}
/*
 * foodbakery_counter_image
 */
function foodbakery_counter_image(value, id) {
    var $ = jQuery;

    if (value == "icon") {
        jQuery(".selected_image_type" + id).hide();
        jQuery(".selected_icon_type" + id).show();
    } else {
        jQuery(".selected_image_type" + id).show();
        jQuery(".selected_icon_type" + id).hide();
    }

}
/*
 * foodbakery_counter_view_type
 */
function foodbakery_counter_view_type(value, id) {
    var $ = jQuery;

    if (value == "icon-border") {
        jQuery("#selected_view_icon_type" + id).hide();
        jQuery("#selected_view_border_type" + id).show();
        jQuery("#selected_view_icon_image_type" + id).hide();
        jQuery("#selected_view_icon_icon_type" + id).show();
    } else {
        jQuery("#selected_view_icon_type" + id).show();
        jQuery("#selected_view_border_type" + id).hide();
        jQuery("#selected_view_icon_image_type" + id).show();
    }

}

/*
 * foodbakery_service_toggle_image
 */
function foodbakery_service_toggle_image(value, id, object) {
    var $ = jQuery;
    var selectedValue = $('#foodbakery_service_type-' + id).val();
    if (value == "image") {
        jQuery("#modern-size-" + id).hide();
        jQuery("#selected_image_type" + id).show();
        jQuery("#selected_icon_type" + id).hide();

    } else if (value == "icon") {
        if (selectedValue == 'modern') {
            jQuery("#modern-size-" + id).show();
        } else {
            jQuery("#modern-size-" + id).hide();
        }

        jQuery("#selected_image_type" + id).hide();
        jQuery("#selected_icon_type" + id).show();
    }

}
/*
 * foodbakery_service_toggle_view
 */
function foodbakery_service_toggle_view(value, id, object) {
    var $ = jQuery;
    if (value == "modern") {
        jQuery("#foodbakery-service-bg-color-" + id).show();
        jQuery("#modern-size-" + id).show();
        jQuery("#service-position-classic-" + id).hide();
        jQuery("#service-position-modern-" + id).show();
        jQuery("#foodbakery-modern-bg-color-" + id + " #bg-service").html('Button bg Color');

    } else if (value == "classic") {
        jQuery("#modern-size-" + id).hide();
        jQuery("#foodbakery-service-bg-color-" + id).hide();
        jQuery("#service-position-modern-" + id).hide();
        jQuery("#service-position-classic-" + id).show();
        jQuery("#foodbakery-modern-bg-color-" + id + " #bg-service").html('Text Color');
    }

}
/*
 * foodbakery_icon_toggle_view
 */
function foodbakery_icon_toggle_view(value, id, object) {
    var $ = jQuery;
    if (value == "bg_style") {
        jQuery("#selected_icon_view_" + id + " #label-icon").html('Icon Background Color');

    } else if (value == "border_style") {
        jQuery("#selected_icon_view_" + id + " #label-icon").html('Border Color');
    }

}
/*
 * Counter Image Show Hide End
 */

/*
 * CPricetable Title Show Hide Start
 */

function foodbakery_pricetable_style_vlaue(value, id) {
    var $ = jQuery;
    if (value == "classic") {
        jQuery("#pricetbale-title" + id).hide();
    } else {
        jQuery("#pricetbale-title" + id).show();
    }
}
/*
 * show_sidebar
 */

function show_sidebar(id, random_id) {
    "use strict";
    var $ = jQuery;
    jQuery(document).on('click', 'input[class="radio_foodbakery_sidebar]', function (event) {
        jQuery(this).parent().parent().find(".check-list").removeClass("check-list");
        jQuery(this).siblings("label").children("#check-list").addClass("check-list");
    });
    var randomeID = "#" + random_id;
    if ((id == 'left') || (id == 'right')) {
        $(randomeID + "_sidebar_right," + randomeID + "_sidebar_left").hide();
        $(randomeID + "_sidebar_" + id).show();
    } else if ((id == 'both') || (id == 'none')) {
        $(randomeID + "_sidebar_right," + randomeID + "_sidebar_left").hide();
    }
}

/*
 * show_sidebar_page
 */
/*
 function show_sidebar_page(id) {
 var $ = jQuery;
 jQuery(document).on('click', 'input[name="foodbakery_var_page_layout"]', function () {
 jQuery(this).parent().parent().find(".check-list").removeClass("check-list");
 jQuery(this).siblings("label").children("#check-list").addClass("check-list");
 });
 if ((id == 'left') || (id == 'right')) {
 $("#wrapper_sidebar_left,#wrapper_sidebar_right").hide();
 $("#wrapper_sidebar_" + id).show();
 } else if (id == 'both') {
 $("#wrapper_sidebar_right,#wrapper_sidebar_left").show();
 } else if (id == 'none') {
 $("#wrapper_sidebar_right,#wrapper_sidebar_left").hide();
 }
 }
 */
/*
 * foodbakery_toggle_gal
 */

function foodbakery_toggle_gal(id, counter) {
    if (id == 0) {
        jQuery("#link_url" + counter).hide();
        jQuery("#video_code" + counter).hide();
    } else if (id == 1) {
        jQuery("#link_url" + counter).hide();
        jQuery("#video_code" + counter).show();
    } else if (id == 2) {
        jQuery("#link_url" + counter).show();
        jQuery("#video_code" + counter).hide();
    }
}

var _commonshortcode = (function (id) {
    var mainConitem = jQuery("#" + id)
    var totalItemCon = mainConitem.find(".foodbakery-wrapp-clone").size();
    mainConitem.find(".fieldCounter").val(totalItemCon);
    mainConitem.sortable({
        cancel: '.foodbakery-clone-append .form-elements,.foodbakery-disable-true',
        placeholder: "ui-state-highlight"
    });

});
var counter_ingredient = 0;
var html_popup = "<div id='confirmOverlay' style='display:block'> \
								<div id='confirmBox'><div id='confirmText'>Are you sure to do this?</div> \
								<div id='confirmButtons'><div class='button confirm-yes'>Delete</div>\
								<div class='button confirm-no'>Cancel</div><br class='clear'></div></div></div>"


//page Section items delete start
jQuery(document).on("click", ".btndeleteitsection", function () {

    jQuery(this).parents(".parentdeletesection").addClass("warning");
    jQuery(this).parent().append(html_popup);

    jQuery(document).on('click', '.confirm-yes', function (event) {
        jQuery(this).parents(".parentdeletesection").fadeOut(400, function () {
            jQuery(this).remove();
        });
        jQuery("#confirmOverlay").remove();
        count_widget--;
        if (count_widget == 0)
            jQuery("#add_page_builder_item").removeClass("hasclass");
    });
    jQuery(document).on('click', '.confirm-no', function (event) {
        jQuery(this).parents(".parentdeletesection").removeClass("warning");
        jQuery("#confirmOverlay").remove();
    });
    return false;
});


//page Builder items delete start
var category_delet = '';
jQuery(document).on("click", ".btndeleteit", function () {
    jQuery(this).parents(".parentdelete").addClass("warning");
    jQuery(this).parent().append(html_popup);

    category_delet = jQuery(this).attr('data-catid');

    jQuery(document).on('click', '.confirm-yes', function (event) {
        var prev_parent_id = jQuery(this).closest('.parentdeletesection').attr('id');
        var prev_total_columns = jQuery('#' + prev_parent_id + ' input[name="total_column[]"]').val();
        jQuery('#' + prev_parent_id + ' input[name="total_column[]"]').val(parseInt(prev_total_columns) - parseInt(1));
        jQuery(this).closest(".parentdelete").fadeOut(400, function () {
            jQuery(this).remove();
            jQuery('input[name="deleted_categories"]').val(jQuery('input[name="deleted_categories"]').val() + ',' + category_delet);
        });

        jQuery(this).parents(".parentdelete").each(function () {
            var lengthitem = jQuery(this).parents(".dragarea").find(".parentdelete").size() - 1;
            jQuery(this).parents(".dragarea").find("input.textfld").val(lengthitem);
        });

        jQuery("#confirmOverlay").remove();
        count_widget--;
        if (count_widget == 0)
            jQuery("#add_page_builder_item").removeClass("hasclass");

    });
    jQuery(document).on('click', '.confirm-no', function (event) {
        jQuery(this).parents(".parentdelete").removeClass("warning");
        jQuery("#confirmOverlay").remove();
    });

    return false;
});

/*
 * page Builder items delete end
 */

/*
 * adding social network start
 */

function social_icon_del(id) {
    jQuery("#del_" + id).remove();
    jQuery("#" + id).remove();
}

/*
 * Sidebar Layout
 */

function foodbakery_slider_element_toggle(id) {
    if (id == 'default_header') {
        jQuery("#wrapper_default_header").hide();
        jQuery("#wrapper_breadcrumb_header").hide();
        jQuery("#wrapper_custom_slider").hide();
        jQuery("#wrapper_map").hide();
        jQuery("#wrapper_no-header").hide();
    } else if (id == 'custom_slider') {
        jQuery("#wrapper_custom_slider").show();
        jQuery("#wrapper_default_header").hide();
        jQuery("#wrapper_breadcrumb_header").hide();
        jQuery("#wrapper_map").hide();
        jQuery("#wrapper_no-header").hide();
    } else if (id == 'no-header') {
        jQuery("#wrapper_no-header").show();
        jQuery("#wrapper_default_header").hide();
        jQuery("#wrapper_breadcrumb_header").hide();
        jQuery("#wrapper_custom_slider").hide();
        jQuery("#wrapper_map").hide();
    } else if (id == 'breadcrumb_header') {
        jQuery("#wrapper_breadcrumb_header").show();
        jQuery("#wrapper_default_header").show();
        jQuery("#wrapper_custom_slider").hide();
        jQuery("#wrapper_map").hide();
        jQuery("#wrapper_no-header").hide();
    } else if (id == 'map') {
        jQuery("#wrapper_map").show();
        jQuery("#wrapper_default_header").hide();
        jQuery("#wrapper_breadcrumb_header").hide();
        jQuery("#wrapper_custom_slider").hide();
        jQuery("#wrapper_no-header").hide();
    } else {
        jQuery("#wrapper_default_header").hide();
        jQuery("#wrapper_breadcrumb_header").hide();
        jQuery("#wrapper_custom_slider").hide();
        jQuery("#wrapper_map").hide();
        jQuery("#wrapper_no-header").hide();
    }

}

/*
 * toggle hide/show
 */
function foodbakery_hide_show_toggle(id, div, type) {

    if (type == 'theme_options') {
        if (id == 'default') {
            jQuery("#foodbakery_sh_paddingtop_range").hide();
            jQuery("#foodbakery_sh_paddingbottom_range").hide();
        } else if (id == 'custom') {
            jQuery("#foodbakery_sh_paddingtop_range").show();
            jQuery("#foodbakery_sh_paddingbottom_range").show();
        }

    } else {
        if (id == 'default') {
            jQuery("#" + div).hide();
        } else if (id == 'custom') {
            jQuery("#" + div).show();
        }
    }
}

/*
 * background options
 */

function foodbakery_section_background_settings_toggle(id, rand_no) {

    if (id == "no-image") {
        jQuery(".section-custom-background-image-" + rand_no).hide();
        jQuery(".section-slider-" + rand_no).hide();
        jQuery(".section-custom-slider-" + rand_no).hide();
        jQuery(".section-background-video-" + rand_no).hide();
    } else if (id == "section-custom-background-image") {
        jQuery(".section-slider-" + rand_no).hide();
        jQuery(".section-custom-slider-" + rand_no).hide();
        jQuery(".section-background-video-" + rand_no).hide();
        jQuery(".section-custom-background-image-" + rand_no).show();
    } else if (id == "section-slider") {
        jQuery(".section-custom-background-image-" + rand_no).hide();
        jQuery(".section-slider-" + rand_no).show();
        jQuery(".section-custom-slider-" + rand_no).hide();
        jQuery(".section-background-video-" + rand_no).hide();

    } else if (id == "section-custom-slider") {
        jQuery(".section-custom-background-image-" + rand_no).hide();
        jQuery(".section-slider-" + rand_no).hide();
        jQuery(".section-custom-slider-" + rand_no).show();
        jQuery(".section-background-video-" + rand_no).hide();

    } else if (id == "section_background_video") {
        jQuery(".section-custom-background-image-" + rand_no).hide();
        jQuery(".section-slider-" + rand_no).hide();
        jQuery(".section-custom-slider-" + rand_no).hide();
        jQuery(".section-background-video-" + rand_no).show();

    } else {
        jQuery(".section-custom-background-image-" + rand_no).hide();
        jQuery(".section-slider-" + rand_no).hide();
        jQuery(".section-custom-slider-" + rand_no).hide();
        jQuery(".section-background-video-" + rand_no).hide();
    }
}


/*
 * thumbnail view
 */

function foodbakery_thumbnail_view(id) {
    if (id == "none") {
        jQuery("#wrapper_thumb_slider").hide();
        jQuery("#wrapper_post_thumb_image").hide();

    } else if (id == "single") {
        jQuery("#wrapper_thumb_slider").hide();
        jQuery("#wrapper_post_thumb_image").show();
        jQuery("#wrapper_thumb_audio").hide();
    } else if (id == "slider") {
        jQuery("#wrapper_post_thumb_image").hide();
        jQuery("#wrapper_thumb_slider").show();
        jQuery("#wrapper_thumb_audio").hide();
    } else if (id == "audio") {
        jQuery("#wrapper_post_thumb_image").hide();
        jQuery("#wrapper_thumb_slider").hide();
        jQuery("#wrapper_thumb_audio").show();
    }


}
/*
 * post view
 */
function foodbakery_post_view(id) {
    if (id == "single") {
        jQuery("#wrapper_post_detail, #wrapper_post_detail_slider, #wrapper_audio_view, #wrapper_video_view").hide();
        jQuery("#wrapper_post_detail").show();
    } else if (id == "audio") {
        jQuery("#wrapper_post_detail, #wrapper_post_detail_slider, #wrapper_audio_view, #wrapper_video_view").hide();
        jQuery("#wrapper_audio_view").show();
    } else if (id == "video") {
        jQuery("#wrapper_post_detail, #wrapper_post_detail_slider, #wrapper_audio_view, #wrapper_video_view").hide();
        jQuery("#wrapper_video_view").show();
    } else if (id == "slider") {
        jQuery("#wrapper_post_detail, #wrapper_post_detail_slider, #wrapper_audio_view, #wrapper_video_view").hide();
        jQuery("#wrapper_post_detail_slider").show();
    } else {
        jQuery("#wrapper_post_detail, #wrapper_post_detail_slider, #wrapper_audio_view, #wrapper_video_view").hide();
    }
}

/*
 * show slider
 */
function foodbakery_show_slider(value) {
    if (value == 'Revolution Slider') {
        jQuery('#tab-sub-header-options ul,#tab-sub-header-options #foodbakery_background_img_box').hide();
        jQuery('#foodbakery_default_header_header').show();
        jQuery('#foodbakery_custom_slider_1').show();
    } else if (value == 'No sub Header') {
        jQuery('#tab-sub-header-options ul,#tab-sub-header-options #foodbakery_background_img_box').not('#tab-sub-header-options ul#foodbakery_header_border_color_color').hide();
        jQuery('#foodbakery_default_header_header,#tab-sub-header-options ul#foodbakery_header_border_color_color').show();
    } else {
        jQuery('#tab-sub-header-options ul,#tab-sub-header-options #foodbakery_background_img_box').show();
        jQuery('#foodbakery_custom_slider_1,#foodbakery_header_border_color_color').hide();
    }
}
/*
 * add field
 */

function foodbakery_add_field(id, type) {
    var wrapper = jQuery("#" + id + " .input_fields_wrap"); //Fields wrapper
    var items = jQuery("#" + id + " .input_fields_wrap > div").length + 1;

    var uniqueNum = type + '_' + Math.floor(Math.random() * 99999);

    var remove = 'javascript:foodbakery_remove_field("' + uniqueNum + '","' + id + '")';

    jQuery("#" + id + "  .counter_num").val(items);

    jQuery(wrapper).append('<div class="foodbakery-wrapp-clone foodbakery-shortcode-wrapp  foodbakery-pbwp-content" id="' + uniqueNum + '"><ul class="form-elements bcevent_title"><li class="to-label"><label>Pricing Feature ' + items + '</label></li><li class="to-field"><div class="input-sec"><input class="txtfield" type="text" value="" name="pricing_feature[]"></div><div id="price_remove"><a class="remove_field" onclick=' + remove + '><i class="icon-minus-circle" style="color:#000; font-size:18px"></i></div></a></li></ul></div>'); //add input box
}
/*
 * remove field
 */

function foodbakery_remove_field(id, wrapper) {
    var totalItems = jQuery("#" + wrapper + "  .counter_num").val() - 1;
    jQuery("#" + wrapper + "  .counter_num").val(totalItems);
    jQuery("#" + wrapper + " #" + id + "").remove();
}

jQuery('#tab-location-settings-foodbakery-events').bind('tabsshow', function (event, ui) {
    if (ui.panel.id == "map-tab") {
        resizeMap();
    }
});
/*
 * createclone
 */
function _createclone(object, id, section, post) {

    var _this = object.closest(".column");
    _this.clone().insertAfter(_this);
    callme();
    jQuery(".draginner").sortable({
        connectWith: '.draginner',
        handle: '.column-in',
        cancel: '.draginner .poped-up,#confirmOverlay',
        revert: false,
        start: function (event, ui) {
            jQuery(ui.item).css({"width": "25%"})
        },
        receive: function (event, ui) {
            callme();
            getsorting(ui)
        },
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true
    });
    return false;
}
/*
 * ajax shortcode widget element
 */
/*
 function ajax_shortcode_widget_element(object, admin_url, POSTID, name) {
 var action = '';
 var wraper = object.closest(".column-in").next();
 var _structure = "<div id='foodbakery-pbwp-outerlay'><div id='foodbakery-widgets-list'></div></div>";
 
 jQuery(wraper).wrap(_structure).delay(100).fadeIn(150);
 var shortcodevalue = object.closest(".column-in").next().find(".foodbakery-textarea-val").val();
 if (shortcodevalue) {
 
 var elementnamevalue = object.closest(".column-in").next().find(".foodbakery-dcpt-element").val();
 SuccessLoader();
 //_createpop(wraper, "filterdrag");
 counter++;
 var dcpt_element_data = '';
 if (elementnamevalue) {
 var dcpt_element_data = '&element_name=' + elementnamevalue;
 }
 var newCustomerForm = "action=foodbakery_pb_" + name + '&counter=' + Math.floor((Math.random() * 100000) + 1) + '&shortcode_element_id=' + encodeURIComponent(shortcodevalue) + '&POSTID=' + POSTID + dcpt_element_data;
 var edit_url = action + counter;
 //_createpop();
 jQuery.ajax({
 type: "POST",
 url: admin_url,
 data: newCustomerForm,
 success: function (data) {
 var rsponse = data;
 var response_html = jQuery(rsponse).find(".foodbakery-pbwp-content").html();
 object.closest(".column-in").next().find(".pagebuilder-data-load").html(response_html);
 object.closest(".column-in").next().find(".foodbakery-wiget-element-type").val('form');
 jQuery('.loader').remove();
 jQuery('.bg_color').wpColorPicker();
 jQuery('div.foodbakery-drag-slider').each(function () {
 var _this = jQuery(this);
 _this.slider({
 range: 'min',
 step: _this.data('slider-step'),
 min: _this.data('slider-min'),
 max: _this.data('slider-max'),
 value: _this.data('slider-value'),
 slide: function (event, ui) {
 jQuery(this).parents('li.to-field').find('.foodbakery-range-input').val(ui.value)
 }
 });
 });
 jQuery(".draginner").sortable({
 connectWith: '.draginner',
 handle: '.column-in',
 cancel: '.draginner .poped-up,#confirmOverlay',
 revert: false,
 receive: function (event, ui) {
 callme();
 getsorting(ui)
 },
 placeholder: "ui-state-highlight",
 forcePlaceholderSize: true
 
 });
 }
 });
 }
 }
 */
/*
 * aremoverlay
 */

function _removerlay(object) {
    jQuery("#foodbakery-widgets-list .loader").remove();
    var _elem1 = "<div id='foodbakery-pbwp-outerlay'></div>",
            _elem2 = "<div id='foodbakery-widgets-list'></div>";
    var $elem;
    $elem = object.closest('div[class*="foodbakery-wrapp-class-"]');
    $elem.unwrap();
    $elem.unwrap();
    $elem.hide()
}
/*
 * create pop short
 */
function _createpopshort(object) {

    var _structure = "<div id='foodbakery-pbwp-outerlay'><div id='foodbakery-widgets-list'></div></div>";
    //$elem = jQuery('#foodbakery-widgets-list');

    var a = object.closest(".column-in").next();
    jQuery(a).wrap(_structure).delay(100).fadeIn(150);




}

// Post xml import

/*
 * Header Options
 */

// 
function foodbakery_header_option(val) {
    if (val == 'none') {
        jQuery('#wrapper_rev_slider,#wrapper_headerbg_image').hide();
    } else if (val == 'foodbakery_rev_slider') {
        jQuery('#wrapper_rev_slider').fadeIn();
        jQuery('#wrapper_headerbg_image').hide();
    } else if (val == 'foodbakery_bg_image_color') {
        jQuery('#wrapper_headerbg_image').fadeIn();
        jQuery('#wrapper_rev_slider').hide();
    }
}

/*
 * banner widget toggle
 */

function foodbakery_banner_widget_toggle(view, id) {
    if (view == 'random') {
        jQuery("#foodbakery_banner_style_field_" + id).show();
        jQuery("#foodbakery_banner_code_field_" + id).hide();
        jQuery("#foodbakery_banner_number_field_" + id).show();
    } else if (view == 'single') {
        jQuery("#foodbakery_banner_style_field_" + id).hide();
        jQuery("#foodbakery_banner_code_field_" + id).show();
        jQuery("#foodbakery_banner_number_field_" + id).hide();
    }
}
/**
 * add qual list
 *
 */
var counter_qual = 0;
function add_qual_list(admin_url, theme_url) {

    counter_qual++;
    var dataString = 'foodbakery_qual_name=' + jQuery("#foodbakery_qual_name").val() +
            '&foodbakery_qual_desc=' + jQuery("#foodbakery_qual_desc").val() +
            '&action=add_qual_to_list';
    jQuery(".feature-loader").html("<i class='icon-spinner8 icon-spin'></i>");
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery("#total_quals").append(response);
            jQuery(".feature-loader").html("");
            removeoverlay('add_qual_title', 'append');
            jQuery("#foodbakery_qual_name").val("Title");
            jQuery("#foodbakery_qual_desc").val("");
        }
    });
    return false;
}
/**
 * schedule list
 *
 */
var counter_schedule = 0;
function add_schedule_list(admin_url, theme_url) {

    counter_schedule++;
    var dataString = 'foodbakery_schedule_name=' + jQuery("#foodbakery_schedule_name").val() +
            '&foodbakery_schedule_time=' + jQuery("#foodbakery_schedule_time").val() +
            '&foodbakery_schedule_desc=' + jQuery("#foodbakery_schedule_desc").val() +
            '&action=add_schedule_to_list';
    jQuery(".feature-loader").html("<i class='icon-spinner8 icon-spin'></i>");
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery("#total_schedules").append(response);
            jQuery(".feature-loader").html("");
            removeoverlay('add_schedule_title', 'append');
            jQuery("#foodbakery_schedule_name").val("Title");
            jQuery("#foodbakery_schedule_time").val("");
            jQuery("#foodbakery_schedule_desc").val("");
        }
    });
    return false;
}
/**
 * camp sched list
 *
 */
var counter_camp_sched = 0;
function add_camp_sched_list(admin_url, theme_url) {

    counter_camp_sched++;
    var dataString = 'foodbakery_camp_sched_name=' + jQuery("#foodbakery_camp_sched_name").val() +
            '&foodbakery_camp_sched_time=' + jQuery("#foodbakery_camp_sched_time").val() +
            '&foodbakery_camp_sched_loc=' + jQuery("#foodbakery_camp_sched_loc").val() +
            '&foodbakery_camp_sched_desc=' + jQuery("#foodbakery_camp_sched_desc").val() +
            '&action=add_camp_sched_to_list';
    jQuery(".feature-loader").html("<i class='icon-spinner8 icon-spin'></i>");
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery("#total_camp_scheds").append(response);
            jQuery(".feature-loader").html("");
            removeoverlay('add_camp_sched_title', 'append');
            jQuery("#foodbakery_camp_sched_name").val("Title");
            jQuery("#foodbakery_camp_sched_time").val("");
            jQuery("#foodbakery_camp_sched_loc").val("");
            jQuery("#foodbakery_camp_sched_desc").val("");
        }
    });
    return false;
}

function foodbakery_display_url_field(id) {
    "use strict";
    if (id == 'yes') {
        jQuery("#advance_url_field").show();
    } else {
        jQuery("#advance_url_field").hide();
        jQuery("#foodbakery_job_advance_search_url").val('');

    }
    return true;
}

//send smtp mail
function send_smtp_mail(admin_url) {
    "use strict";
    jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);
    var candidate_skills_calc = 0;

    var serializedValues = jQuery("#plugin-options input,#plugin-options select,#plugin-options textarea,#plugin-options checkbox").serialize() + '&action=send_smtp_mail';

    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: serializedValues,
        success: function (response) {
            jQuery(".loading_div").hide();
            jQuery(".form-msg .innermsg").html(response);
            jQuery(".form-msg").show();
            jQuery(".outerwrapp-layer").fadeTo(2000, 1000).slideUp(1000);
            slideout();
        }
    });
}

function foodbakery_mail_with_gmail(opt_id) {
    jQuery("#foodbakery_smtp_host").val('smtp.gmail.com');
    jQuery("#foodbakery_smtp_port").val('465');
    jQuery("#foodbakery_secure_connection_type").val('ssl');
    alert('You must provide your Gmail Email address as SMTP username and Gmail Password as SMTP Password.');
}
function use_smtp_mail_opt(thisObj) {
    var opt_id = jQuery(thisObj).data('id');
    if (jQuery("#" + opt_id).val() == 'on') {
        jQuery("#foodbakery-no-smtp-div").show();
    } else {
        jQuery("#foodbakery-no-smtp-div").hide();
    }
}

function use_wooC_gateways(thisObj) {
    var opt_id = jQuery(thisObj).data('id');
    if (jQuery("#" + opt_id).val() == 'on') {
        jQuery("#foodbakery-no-wooC-gateway-div").hide();
    } else {
        jQuery("#foodbakery-no-wooC-gateway-div").show();
    }
}
/*
 * chosen selection box
 */


/*
 * Custom Fields for Restaurant Type
 */
function foodbakery_custom_fields_js() {
    var parentItem = jQuery("#foodbakery-pb-formelements");
    parentItem.sortable({
        cancel: 'div div.poped-up,.pb-toggle',
        handle: ".pbwp-legend",
        placeholder: "ui-state-highlighter"
    });
}

jQuery(document).on('click', 'img.pbwp-clone-field', function () {
    var _this = jQuery(this),
            b = _this.closest('div.pbwp-clone-field');
    var dataString = b.clone().html();
    var counter = $("pbwp-clone-field").length;
    var dataResponse = dataString.replace(/foodbakery_cus_field_dropdown_options_imgs/g, 'foodbakery_cus_field_dropdown_options_imgs' + counter + 1);
    jQuery('<div class="pbwp-clone-field clearfix">' + dataResponse + '</div>').insertAfter(b);
    var a = _this.parents('.pbwp-form-sub-fields').find('input:radio');
    a.each(function (index, el) {
        jQuery(this).val(index + 1);
    });
});

jQuery(document).on('click', 'img.pbwp-remove-field', function () {
    jQuery(this).parent('.pbwp-clone-field').remove();
});

jQuery(document).on('click', 'a.pbwp-toggle', function () {
    jQuery(this).parents(".pbwp-legend").next().slideToggle(300);
});

jQuery(document).on('click', '.pbwp-remove', function () {
    var a = confirm("This will delete Item");
    if (a) {
        jQuery(this).parents(".pb-item-container").remove();
    }
});

/*
 * Custom Fields for Restaurant Type
 */
function foodbakery_custom_fields_form_builder_js() {
    var parentItem = jQuery("#foodbakery-pb-formelements_form_builder");
    parentItem.sortable({
        cancel: 'div div.poped-up,.pb-toggle',
        handle: ".pbwp-legend",
        placeholder: "ui-state-highlighter"
    });
    var c = 0;
    parentItem.on("click", "img.pbwp-clone-field", function (e) {
        e.preventDefault();
        var _this = jQuery(this),
                b = _this.closest('div.pbwp-clone-field');
        var dataString = b.clone().html();
        var counter = $("pbwp-clone-field").length;
        var dataResponse = dataString.replace(/foodbakery_cus_field_dropdown_options_imgs/g, 'foodbakery_cus_field_dropdown_options_imgs' + counter + 1);
        jQuery('<div class="pbwp-clone-field clearfix">' + dataResponse + '</div>').insertAfter(b);
        var a = _this.parents('.pbwp-form-sub-fields').find('input:radio');
        a.each(function (index, el) {
            jQuery(this).val(index + 1);
        });

    });

    parentItem.on("click", "img.pbwp-remove-field", function (e) {
        jQuery(this).parent('.pbwp-clone-field').remove();
    });
    parentItem.on("click", ".pbwp-remove", function (e) {
        e.preventDefault();
        var a = confirm("This will delete Item");
        if (a) {
            jQuery(this).parents(".pb-item-container").remove();
        }
    });
    parentItem.on("click", "a.pbwp-toggle", function (e) {
        e.preventDefault();
        jQuery(this).parents(".pbwp-legend").next().slideToggle(300);
    });
}


function foodbakery_createpop(data, type) {
    "use strict";
    var _structure = "<div id='foodbakery-pbwp-outerlay'><div id='foodbakery-widgets-list'></div></div>",
            $elem = jQuery('#foodbakery-widgets-list');
    jQuery('body').addClass("foodbakery-overflow");
    if (type == "csmedia") {
        $elem.append(data);
    }
    if (type == "filter") {
        jQuery('#' + data).wrap(_structure).delay(100).fadeIn(150);
        jQuery('#' + data).parent().addClass("wide-width");
    }
    if (type == "filterdrag") {
        jQuery('#' + data).wrap(_structure).delay(100).fadeIn(150);
    }

    if (jQuery('.chosen-select, .chosen-select-deselect, .chosen-select-no-single, .chosen-select-no-results, .chosen-select-width').length != '') {
        var config = {
            '.chosen-select': {width: "100%"},
            '.chosen-select-deselect': {allow_single_deselect: true},
            '.chosen-select-no-single': {disable_search_threshold: 4, width: "100%"},
            '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chosen-select-width': {width: "95%"}
        }
        for (var selector in config) {
            jQuery(selector).chosen(config[selector]);
        }
    }

}

function add_restaurant_feature(admin_url) {

    var dataString = 'foodbakery_feature_name=' + jQuery("#foodbakery_feature_name").val() + '&foodbakery_feature_icon=' + jQuery("#e9_element_feature_icon").val() + '&action=add_feature_to_list';
    jQuery(".feature-loader").html("<i class='icon-spinner icon-spin'></i>");
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery("#total_features").append(response);
            jQuery(".feature-loader").html("");
            foodbakery_removeoverlay('add_feature_title', 'append');
            jQuery("#foodbakery_feature_name").val("Title");
        }
    });
    return false;
}

function change_tag_value(changeID, changeValue) {
    jQuery('#' + changeID).html(changeValue);
}

function add_restaurant_category(admin_url) {

    var dataString = 'foodbakery_category_name=' + jQuery("#foodbakery_category_name").val() + '&foodbakery_category_parent=' + jQuery("#foodbakery_category_parent").val() + '&foodbakery_restaurant_taxonomy_icons=' + jQuery("#e9_element_restaurant_type_icons").val() + '&action=add_category_to_list';
    jQuery(".category-loader").html("<i class='icon-spinner icon-spin'></i>");
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery("#total_categories").append(response);
            jQuery(".category-loader").html("");
            foodbakery_removeoverlay('add_category_title', 'append');
            jQuery("#foodbakery_category_name").val("Title");
        }
    });
    return false;
}
/*
 function add_restaurant_category(admin_url) {
 
 var dataString = 'foodbakery_category_name=' + jQuery("#foodbakery_category_name").val() + '&action=add_category_to_list';
 jQuery(".category-loader").html("<i class='icon-spinner icon-spin'></i>");
 jQuery.ajax({
 type: "POST",
 url: admin_url,
 data: dataString,
 success: function (response) {
 jQuery("#total_categories").append(response);
 jQuery(".category-loader").html("");
 foodbakery_removeoverlay('add_category', 'append');
 //jQuery("#foodbakery_category_name").val("Title");
 }
 });
 return false;
 }
 */
function foodbakery_removeoverlay(id, text) {
    "use strict";
    jQuery("#foodbakery-widgets-list .loader").remove();
    var _elem1 = "<div id='foodbakery-pbwp-outerlay'></div>",
            _elem2 = "<div id='foodbakery-widgets-list'></div>",
            $elem = jQuery("#" + id);
    jQuery("#foodbakery-widgets-list").unwrap();
    if (text == "append" || text == "filterdrag") {
        $elem.hide().unwrap();
    }
    if (text == "widgetitem") {
        $elem.hide().unwrap();
        jQuery("body").append("<div id='foodbakery-pbwp-outerlay'><div id='foodbakery-widgets-list'></div></div>");
        return false;

    }
    if (text == "ajax-drag") {
        jQuery("#foodbakery-widgets-list").remove();
    }
    jQuery("body").removeClass("foodbakery-overflow");
}

function foodbakery_check_fields_avail() {
    "use strict";
    jQuery('input[id^="check_field_name"]').change(function (e) {
        var foodbakery_ajaxurl = jQuery('#tabbed-content').data('ajax-url');
        var doneTypingInterval = 1000; //time in ms, 5 second for example
        var name = jQuery(this).val();
        var serializedValues = jQuery("form").serialize();
        var $this = jQuery(this);
        var dataString = 'name=' + name +
                '&form_field_names=' + serializedValues +
                '&action=foodbakery_check_fields_avail'

        setTimeout(function () {

            $this.next('span').html('<i class="icon-spinner icon-spin"></i>');
            jQuery.ajax({
                type: "POST",
                url: foodbakery_ajaxurl,
                data: dataString,
                dataType: 'json',
                success: function (response) {
                    if (response.type == 'success') {
                        $this.next('.name-checking').html(response.message);
                        jQuery('input[type="button"]').removeAttr('disabled');
                    } else if (response.type == 'error') {
                        $this.next('.name-checking').html(response.message);
                        jQuery('input[type="button"]').attr('disabled', 'disabled');
                    }
                }
            });
        }, doneTypingInterval)

    });
}

jQuery(document).on('change', '.dir_meta_key_field', function (e) {
    var foodbakery_ajaxurl = jQuery('#tabbed-content').data('ajax-url');
    var doneTypingInterval = 1000; //time in ms, 5 second for example
    var name = jQuery(this).val();
    var serializedValues = jQuery("form").serialize();
    var $this = jQuery(this);
    var dataString = 'name=' + name +
            '&form_field_names=' + serializedValues +
            '&action=foodbakery_check_fields_avail'

    $this.next('span').html('<i class="icon-spinner icon-spin"></i>');
    jQuery.ajax({
        type: "POST",
        url: foodbakery_ajaxurl,
        data: dataString,
        dataType: 'json',
        success: function (response) {
            if (response.type == 'success') {
                $this.next('.name-checking').html('<em class="name-check-pass">' + response.message + '</em>');
                $this.parents('.pb-item-container').find('.pbwp-legend').removeClass('item-field-error');
                $this.removeClass('meta-field-error');
            } else if (response.type == 'error') {
                $this.addClass('admin-field-error');
                $this.addClass('meta-field-error');
                $this.next('.name-checking').html('<em class="name-check-error">' + response.message + '</em>');
                $this.parents('.pb-item-container').find('.pbwp-legend').addClass('item-field-error');
            }
        }
    });

});


jQuery(document).on('change', '.dir-res-meta-key-field', function (e) {
    var foodbakery_ajaxurl = jQuery('#tabbed-content').data('ajax-url');
    var doneTypingInterval = 1000; //time in ms, 5 second for example
    var name = jQuery(this).val();
    var serializedValues = jQuery("form").serialize();
    var $this = jQuery(this);
    var dataString = 'name=' + name +
            '&form_field_names=' + serializedValues +
            '&action=foodbakery_check_reservation_fields_avail'

    $this.next('span').html('<i class="icon-spinner icon-spin"></i>');
    jQuery('input[type="submit"]').attr('disabled', 'disabled');
    jQuery.ajax({
        type: "POST",
        url: foodbakery_ajaxurl,
        data: dataString,
        dataType: 'json',
        success: function (response) {
            if (response.type == 'success') {
                $this.next('.name-checking').html('<em class="name-check-pass">' + response.message + '</em>');
                jQuery('input[type="submit"]').removeAttr('disabled');
                $this.parents('.pb-item-container').find('.pbwp-legend').removeClass('item-field-error');
            } else if (response.type == 'error') {
                $this.next('.name-checking').html('<em class="name-check-error">' + response.message + '</em>');
                $this.parents('.pb-item-container').find('.pbwp-legend').addClass('item-field-error');
            }
        }
    });

});

function foodbakery_restaurant_type_change(slug, post_id) {
    "use strict";

    var foodbakery_ajaxurl = jQuery('#tabbed-content').data('ajax-url');
    var dataString = 'restaurant_type_slug=' + slug + '&post_id=' + post_id + '&action=restaurant_type_dyn_fields';
    jQuery('#foodbakery-restaurant-type-field').html('<div class="foodbakery-fields-loader"><i class="icon-spinner icon-spin"></i></div>');
    jQuery.ajax({
        type: "POST",
        url: foodbakery_ajaxurl,
        data: dataString,
        dataType: 'json',
        success: function (response) {
            if (response.restaurant_fields !== 'undefined') {
                jQuery('#foodbakery-restaurant-type-field').html(response.restaurant_fields);
            } else {
                jQuery('#foodbakery-restaurant-type-field').html('Error');
            }
        }
    });
}

function change_feature_value(changeID, changeValue) {
    jQuery('#' + changeID).html(changeValue);
}


function foodbakery_show_user_profile_data(user_ID) {
    var dataString = 'user_profile_id=' + user_ID + '&security=' + foodbakery_globals.security + '&action=foodbakery_posted_by_user_data';
    jQuery('#posted_by_user_data_fields').html('<div class="foodbakery-fields-loader"><i class="icon-spinner icon-spin"></i></div>');
    jQuery.ajax({
        type: "POST",
        url: foodbakery_globals.ajax_url,
        data: dataString,
        success: function (response) {
            jQuery('#posted_by_user_data_fields').html(response);
        }
    });
}


jQuery(function ($) {
    // Product gallery file uploads
    var gallery_frame;

    jQuery('.add_gallery_plugin').on('click', 'input', function (event) {

        var $el = $(this);
        rand_id = $el.data('rand_id');
        button_label = $el.data('button_label');
        multiple = $el.data('multiple');
        foodbakery_var_theme_url = $("#foodbakery_var_theme_url").val();
        $gallery_images = $('#gallery_container_' + rand_id + ' ul.gallery_images');
        foodbakery_var_gallery_id = $('#gallery_container_' + rand_id).data("csid");
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if (gallery_frame) {
            //gallery_frame.open();
            // return;
        }
        if (button_label !== '') {
            button_label = button_label;
        } else {
            button_label = 'Add Gallery Image';
        }
        if (multiple == false) {
            multiple = false;
        } else {
            multiple = true;
        }

        // Create the media frame.
        gallery_frame = wp.media({
            title: "Select Image",
            multiple: multiple,
            library: {type: 'image'},
            button: {text: button_label}
        });

        // When an image is selected, run a callback.
        gallery_frame.on('select', function () {

            var selection = gallery_frame.state().get('selection');

            selection.map(function (attachment) {

                attachment = attachment.toJSON();
                if (attachment.type == 'image') {
                    var gallery_url = attachment.url;
                    var gallery_ID = attachment.id;
                }

                if (attachment.url) {
                    attachment_ids = Math.floor((Math.random() * 965674) + 1);
                    if (multiple == false) {
                        var listItems = jQuery('#gallery_container_' + rand_id + ' ul.gallery_images').children();
                        var count = listItems.length;
                        if (count > 0) {
                            $('#gallery_container_' + rand_id + ' ul.gallery_images img').attr('src', gallery_url);
                            $('#gallery_container_' + rand_id + ' ul.gallery_images input[name="' + foodbakery_var_gallery_id + '"]').val(gallery_ID);
                        } else {
                            $('#gallery_container_' + rand_id + ' ul.gallery_images').append('\
                            <li class="image" data-attachment_id="' + attachment_ids + '">\
                                <img src="' + gallery_url + '" />\
                                <input type="hidden" value="' + gallery_ID + '" name="' + foodbakery_var_gallery_id + '" />\
                                <div class="actions">\
                                    <span><a href="javascript:;" class="delete" title="' + $el.data('delete') + '"><i class="icon-times"></i></a></span>\
                                </div>\
                            </li>');
                        }
                    } else {
                        $('#gallery_container_' + rand_id + ' ul.gallery_images').append('\
                            <li class="image" data-attachment_id="' + attachment_ids + '">\
                                <img src="' + gallery_url + '" />\
                                <input type="hidden" value="' + gallery_ID + '" name="' + foodbakery_var_gallery_id + '_ids[]" />\
                                <div class="actions">\
                                    <span><a href="javascript:;" class="delete" title="' + $el.data('delete') + '"><i class="icon-times"></i></a></span>\
                                </div>\
                            </li>');
                    }
                }

            });
            jQuery('#' + foodbakery_var_gallery_id + '_temp').html('');
        });

        // Finally, open the modal.
        gallery_frame.open();
    });
});

/*
 * Gallery Number of Items
 */
function gal_num_of_items(id, rand_id, numb) {
    var foodbakery_var_gal_count = 0;
    jQuery("#gallery_sortable_" + rand_id + " > li").each(function (index) {
        foodbakery_var_gal_count++;
        jQuery('input[name="foodbakery_' + id + '_num"]').val(foodbakery_var_gal_count);
    });

    if (numb != '') {
        var foodbakery_var_data_temp = jQuery('#foodbakery_var_' + id + '_temp');
        if (jQuery('input[name="foodbakery_' + id + '_num"]').val() == numb) {
            foodbakery_var_data_temp.html('<input type="hidden" name="foodbakery_' + id + '_ids[]" value="">');
        }
    }
}

/*
 jQuery(".repeater").on('click', function(){
 var repeater_id  = jQuery(this).data('id');
 var cloneCount = jQuery("#"+repeater_id +" .repeating_field").length;
 clonedDiv = jQuery( "#"+repeater_id ).clone();
 clonedDiv.appendTo( "#"+repeater_id+"_fields" );
 set_cloning_id(repeater_id);
 jQuery("#services_repeater_fields #"+repeater_id).show();
 });
 
 function set_cloning_id( repeater_id ){
 jQuery("#"+repeater_id +" .repeating_field").each(function( index, element ) {
 var current_id   = jQuery(this).attr('id');
 jQuery(this).attr('id',current_id+index);
 });
 }
 */

jQuery(".remove_field").on("click", function () {
    repeater_id = jQuery(this).data('id');
    jQuery(this).parent("#" + repeater_id).remove();
});

jQuery(".repeater").on('click', function () {
    var repeater_id = jQuery(this).data('id') + '_fields';
    var loading_div = jQuery(this).data('id') + '_loader';
    var dataString = 'action=foodbakery_services_repeating_fields&die=true&ajax=true';
    jQuery('#' + loading_div).html('<div class="foodbakery-fields-loader"><i class="icon-spinner icon-spin"></i></div>');
    jQuery.ajax({
        type: "POST",
        url: foodbakery_globals.ajax_url,
        data: dataString,
        success: function (response) {
            jQuery('#' + loading_div).html('');
            jQuery('#' + repeater_id).append(response);
        }
    });
});

jQuery("#services_repeater_fields").sortable({
});

jQuery(".menu_item_repeater").on('click', function () {
    var repeater_id = jQuery(this).data('id') + '_fields';
    var loading_div = jQuery(this).data('id') + '_loader';
    var dataString = 'action=foodbakery_menu_items_repeating_fields&die=true&ajax=true';
    jQuery('#' + loading_div).html('<div class="foodbakery-fields-loader"><i class="icon-spinner icon-spin"></i></div>');
    jQuery.ajax({
        type: "POST",
        url: foodbakery_globals.ajax_url,
        data: dataString,
        success: function (response) {
            jQuery('#' + loading_div).html('');
            jQuery('#' + repeater_id).append(response);
        }
    });
});
jQuery("#menu_items_repeater_fields").sortable({
});



jQuery('.foodbakery_calendar').calendar({
    startYear: new Date().getFullYear(),
    allowOverlap: true,
    displayWeekNumber: false,
    displayDisabledDataSource: false,
    displayHeader: true,
    alwaysHalfDay: false,
    dataSource: [], // an array of data
    style: 'border',
    enableRangeSelection: false,
    disabledDays: [],
    disabledWeekDays: [],
    hiddenWeekDays: [],
    roundRangeLimits: false,
    contextMenuItems: [], // an array of menu items,
    customDayRenderer: null,
    customDataSourceRenderer: null,
    // Callback Events
    clickDay: add_calendar_date,
    daycontextMenu: null,
    selectRange: null,
    renderEnd: null,
});


jQuery(".foodbakery_calendar_fields input").each(function (index) {
    var dateVal = new Date(jQuery(this).val());
    jQuery(".foodbakery_calendar").calendar({
        dataSource: [
            {
                id: 0,
                name: "Google I/O",
                location: "San Francisco, CA",
                startDate: dateVal,
                endDate: dateVal
            },
        ], // an array of data
    });
});

function add_calendar_date(date_obj) {
    var date_added = date_obj.date.getFullYear() + '-' + (date_obj.date.getMonth() + parseInt(1)) + '-' + date_obj.date.getDate();
    var dateHTML = '<input type="hidden" name="foodbakery_calendar[]" id="date-' + date_added + '" value="' + date_added + '">';
    if (jQuery("#date-" + date_added).length > 0) {
        jQuery("#date-" + date_added).remove();
    } else {
        jQuery(".foodbakery_calendar_fields").append(dateHTML);
    }
}

jQuery(".day-content").on("click", function () {
    jQuery(this).parent('.day').removeAttr('style');
    jQuery(this).parent('.day').toggleClass('active');

});

(function ($) {
    $(function () {
        /*
         * Delete Backup locations.
         */
        $('.backup_locations_generates_area').on('click', '#btn_delete_locations_backup', function () {
            "use strict";
            var var_confirm = window.confirm("This action will delete your selected Backup File. Do you still want to continue?");
            if (var_confirm == true) {
                $(".outerwrapp-layer,.loading_div").fadeIn(100);

                var admin_url = $('.backup_generates_area').data('ajaxurl');
                var file_name = $(this).data('file');

                var dataString = 'file_name=' + file_name + '&action=delete_locations_backup_file';
                $.ajax({
                    type: "POST",
                    url: admin_url,
                    data: dataString,
                    success: function (response) {

                        $(".loading_div").hide();
                        $(".form-msg .innermsg").html(response);
                        $(".form-msg").show();
                        $(".outerwrapp-layer").delay(2000).fadeOut(100);
                        window.location.reload(true);
                        slideout();
                    }
                });
                //return false;
            }
        });

        /*
         * Restore or Import locations.
         */
        $('.backup_locations_generates_area').on('click', '#btn_restore_locations_backup, #btn_import_locations_from_url', function () {
            "use strict";
            $(".outerwrapp-layer,.loading_div").fadeIn(100);

            var admin_url = $('.backup_generates_area').data('ajaxurl');
            var file_name = $(this).data('file');

            var dataString = 'file_name=' + file_name + '&action=restore_locations_backup';

            if (typeof (file_name) === 'undefined') {

                var file_name = $('#bkup_locations_import_url').val();

                var dataString = 'file_name=' + file_name + '&file_path=yes&action=restore_locations_backup';
            }

            $.ajax({
                type: "POST",
                url: admin_url,
                data: dataString,
                success: function (response) {
                    $(".loading_div").hide();
                    $(".form-msg .innermsg").html(response);
                    $(".form-msg").show();
                    $(".outerwrapp-layer").delay(2000).fadeOut(100);
                    window.location.reload(true);
                    slideout();
                }
            });
        });

        /*
         * Delete Restaurant type categories backup.
         */
        $('.backup_restaurant_type_categories_generates_area').on('click', '#btn_delete_restaurant_type_categories_backup', function () {
            "use strict";
            var var_confirm = window.confirm("This action will delete your selected Backup File. Do you still want to continue?");
            if (var_confirm == true) {
                $(".outerwrapp-layer,.loading_div").fadeIn(100);

                var admin_url = $('.backup_generates_area').data('ajaxurl');
                var file_name = $(this).data('file');

                var dataString = 'file_name=' + file_name + '&action=delete_restaurant_type_categories_backup_file';
                $.ajax({
                    type: "POST",
                    url: admin_url,
                    data: dataString,
                    success: function (response) {

                        $(".loading_div").hide();
                        $(".form-msg .innermsg").html(response);
                        $(".form-msg").show();
                        $(".outerwrapp-layer").delay(2000).fadeOut(100);
                        window.location.reload(true);
                        slideout();
                    }
                });
                //return false;
            }
        });

        /*
         * Restore or Import restaurant type categories.
         */
        $('.backup_restaurant_type_categories_generates_area').on('click', '#btn_restore_restaurant_type_categories_backup, #btn_import_restaurant_type_categories_from_url', function () {
            "use strict";
            $(".outerwrapp-layer,.loading_div").fadeIn(100);

            var admin_url = $('.backup_generates_area').data('ajaxurl');
            var file_name = $(this).data('file');

            var dataString = 'file_name=' + file_name + '&action=restore_restaurant_type_categories_backup';

            if (typeof (file_name) === 'undefined') {

                var file_name = $('#bkup_restaurant_type_categories_import_url').val();

                var dataString = 'file_name=' + file_name + '&file_path=yes&action=restore_restaurant_type_categories_backup';
            }

            $.ajax({
                type: "POST",
                url: admin_url,
                data: dataString,
                success: function (response) {
                    $(".loading_div").hide();
                    $(".form-msg .innermsg").html(response);
                    $(".form-msg").show();
                    $(".outerwrapp-layer").delay(2000).fadeOut(100);
                    window.location.reload(true);
                    slideout();
                }
            });
        });
    });
})(jQuery);

function foodbakery_load_location_ajax(postfix, allowed_location_types, location_levels, security) {
    "use strict";
    var $ = jQuery;
    $('#loc_country_' + postfix).change(function () {
        popuplate_data(this, 'country');
    });

    $('#loc_state_' + postfix).change(function () {
        popuplate_data(this, 'state');
    });
    $('#loc_county_' + postfix).change(function () {
        popuplate_data(this, 'county');
    });
    $('#loc_city_' + postfix).change(function () {
        popuplate_data(this, 'city');
    });

    $('#loc_town_' + postfix).change(function () {
        popuplate_data(this, 'town');
    });

    function popuplate_data(elem, type) {

        var plugin_url = $(elem).parents("#locations_wrap").data('plugin_url');
        var ajaxurl = $(elem).parents("#locations_wrap").data('ajaxurl');

        var index = allowed_location_types.indexOf(type);
        if (index + 1 >= allowed_location_types.length) {
            return;
        }
        var location_type = allowed_location_types[ index + 1 ];
        $(".loader-" + location_type + "-" + postfix).html("<img src='" + plugin_url + "/assets/backend/images/ajax-loader.gif' />").show();
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "get_locations_list",
                security: security,
                location_type: location_type,
                location_level: location_levels[ location_type ],
                selector: elem.value,
            },
            dataType: "json",
            success: function (response) {
                if (response.error == true) {
                    return;
                }
                var control_selector = "#loc_" + location_type + "_" + postfix;
                var data = response.data;
                $(control_selector + ' option').remove();
                $(control_selector).append($("<option></option>").attr("value", '').text('Choose...'));
                $.each(data, function (key, term) {
                    $(control_selector).append($("<option></option>").attr("value", term.slug).text(term.name));
                });

                $(".loader-" + location_type + "-" + postfix).html('').hide();
                // Only for style implementation.
                $(".chosen-select").data("placeholder", "Select").trigger('chosen:updated');
            }
        });
    }

    jQuery(document).ready(function (e) {

        //changeMap();
        jQuery('input#foodbakery-search-location').keypress(function (e) {
            if (e.which == '13') {
                e.preventDefault();
                cs_search_map(this.value);
                return false;
            }
        });
        jQuery('#loc_country_restaurant').change(function (e) {
            setAutocompleteCountry('restaurant');
        });
        jQuery('#loc_country_publisher').change(function (e) {
            setAutocompleteCountry('publisher');
        });
        jQuery('#loc_country_default').change(function (e) {
            setAutocompleteCountry('default');
        });
    });
    function setAutocompleteCountry(type) {
        "use strict";
        var country = jQuery('select#loc_country_' + type + ' option:selected').attr('data-name'); /*document.getElementById('country').value;*/
        if (country != '') {
            autocomplete.setComponentRestrictions({'country': country});
        } else {
            autocomplete.setComponentRestrictions([]);
        }
    }

}

function generate_locations_backup(admin_url) {
    "use strict";
    jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);

    var dataString = 'action=generate_locations_backup';
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery(".loading_div").hide();
            jQuery(".form-msg .innermsg").html(response);
            jQuery(".form-msg").show();
            jQuery(".outerwrapp-layer").delay(100).fadeOut(100);
            window.location.reload(true);
            slideout();
        }
    });
    //return false;
}

function foodbakery_custom_fields_script(id) {
    "use strict";
    var parentItem = jQuery("#" + id);
    parentItem.sortable({
        cancel: 'div div.poped-up,.pb-toggle',
        handle: ".pbwp-legend",
        placeholder: "ui-state-highlighter"
    });
    var c = 0;
    parentItem.on("click", "img.pbwp-clone-field", function (e) {
        e.preventDefault();
        var _this = jQuery(this),
                b = _this.closest('div.pbwp-clone-field');
        b.clone().insertAfter(b);
        var a = _this.parents('.pbwp-form-sub-fields').find('input:radio');
        a.each(function (index, el) {
            jQuery(this).val(index + 1);
        });
    });
    parentItem.on("click", "img.pbwp-remove-field", function (e) {
        e.preventDefault();
        var _this = jQuery(this),
                b = _this.closest('.pbwp-form-sub-fields');
        c = b.find('div.pbwp-clone-field').length;
        if (c > 1) {
            _this.closest("div.pbwp-clone-field").remove()
        }
        _this.parents('div.pbwp-clone-field').remove();
    });
    parentItem.on("click", ".pbwp-remove", function (e) {
        e.preventDefault();
        var a = confirm("This will delete Item");
        if (a) {
            jQuery(this).parents(".pb-item-container").remove()
            alertbox();
        }
    })

    parentItem.on("click", "a.pbwp-toggle", function (e) {
        //e.preventDefault();
        //jQuery(this).parents(".pbwp-legend").next().slideToggle(300);
    });
}

function opening_hour_time_lapse(thisObj, divID) {

    inputID = jQuery(thisObj).attr('name');
    var value = jQuery('#' + inputID).val();
    if (value == 'on') {
        jQuery(divID).show();
    } else {
        jQuery(divID).hide();
    }
}

jQuery(document).on('click', 'label.cs-chekbox', function () {
    "use strict";
    var checkbox = jQuery(this).find('input[type=checkbox]');

    if (checkbox.is(":checked")) {
        jQuery(this).find('input[type="hidden"]').val(checkbox.val());
        jQuery(this).find('input[type="hidden"]').attr('value', 'on');
    } else {
        jQuery(this).find('input[type="hidden"]').val('off');
        jQuery(this).find('#input[type="hidden"]').attr('value', 'off');
    }
});

jQuery(document).on("click", ".foodbakery-uploadMedia", function () {
    var $ = jQuery;
    var id = $(this).attr("name");
    //jQuery('input[name="'+id+'"]').hide();
    var custom_uploader = wp.media({
        title: 'Select File',
        button: {
            text: 'Add File'
        },
        multiple: false
    })
            .on('select', function () {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                jQuery('#' + id).val(attachment.id);
                jQuery('#' + id).next().hide();
                jQuery('#' + id + '_img').attr('src', attachment.url);
                jQuery('#' + id + '_box').show();
            }).open();
});

/*
 * Category Delete
 */

jQuery(document).on("click", ".delete-category", function () {
    jQuery(this).parents(".parentdelete").addClass("warning");
    jQuery(this).parent().append(html_popup);

    jQuery(document).on('click', '.confirm-yes', function (event) {
        jQuery(this).parents(".parentdelete").fadeOut(800, function () {
            jQuery(this).remove();
        });
        jQuery("#confirmOverlay").remove();
    });
    jQuery(document).on('click', '.confirm-no', function (event) {
        jQuery(this).parents(".parentdelete").removeClass("warning");
        jQuery("#confirmOverlay").remove();
    });

    return false;
});

jQuery(document).on("click", "#services_repeater label", function () {
    jQuery(this).parent('#services_repeater').toggleClass('active');
});

function add_package_field(admin_url) {

    var default_label = jQuery('#foodbakery_field_label').attr('title');
    var dataString = 'foodbakery_field_label=' + jQuery('#foodbakery_field_label').val() + '&foodbakery_field_type=' + jQuery('#foodbakery_field_type').val() + '&action=add_package_field';
    jQuery('.package-field-loader').html("<i class='icon-spinner icon-spin'></i>");
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery('#package_fields').append(response);
            jQuery('.package-field-loader').html('');
            foodbakery_removeoverlay('add_field_title', 'append');
            jQuery('#foodbakery_field_label').val(default_label);
        }
    });
    return false;
}

jQuery(document).on("click", ".package-field-delete", function () {
    jQuery(this).parents(".parentdelete").addClass("warning");
    jQuery(this).parent().append(html_popup);

    jQuery(document).on('click', '.confirm-yes', function (event) {
        jQuery(this).parents(".parentdelete").fadeOut(800, function () {
            jQuery(this).remove();
        });
        jQuery("#confirmOverlay").remove();
    });
    jQuery(document).on('click', '.confirm-no', function (event) {
        jQuery(this).parents(".parentdelete").removeClass("warning");
        jQuery("#confirmOverlay").remove();
    });

    return false;
});


jQuery(document).on('click', '.book-btn', function () {
    $(this).next('.calendar-holder').slideToggle("fast");
});

jQuery(document).on('click', 'a[id^="foodbakery-dev-day-off-rem-"]', function () {
    var _this_id = $(this).data('id');
    jQuery('#day-remove-' + _this_id).remove();
});

jQuery(document).on('click', '.foodbakery-dev-insert-off-days .foodbakery-dev-calendar-days .day a', function () {
    "use strict";
    $ = jQuery;

    var adding_off_day,
            _this_id = $(this).parents('.foodbakery-dev-insert-off-days').data('id'),
            _ajax_url = $(this).parents('.foodbakery-dev-insert-off-days').data('ajax-url'),
            _plugin_url = $(this).parents('.foodbakery-dev-insert-off-days').data('plugin-url'),
            _day = $(this).data('day'),
            _month = $(this).data('month'),
            _year = $(this).data('year'),
            _this_append = $('#foodbakery-dev-add-off-day-app-' + _this_id),
            no_off_day_msg = _this_append.find('#no-book-day-' + _this_id),
            this_loader = $('#foodbakery-dev-loader-' + _this_id);
    //this_act_msg = $('#foodbakery-dev-act-msg-' + _this_id);
    if (typeof _day !== 'undefined' && typeof _month !== 'undefined' && typeof _year !== 'undefined') {
        this_loader.html('<div class="loader-holder"><img src="' + _plugin_url + 'assets/frontend/images/ajax-loader.gif" alt=""></div>');
        adding_off_day = $.ajax({
            url: _ajax_url,
            method: "POST",
            data: {
                off_day_day: _day,
                off_day_month: _month,
                off_day_year: _year,
                restaurant_add_counter: _this_id,
                action: 'foodbakery_restaurant_off_day_to_list'
            },
            dataType: "json"
        }).done(function (response) {
            if (typeof response.html !== 'undefined') {
                no_off_day_msg.remove();
                _this_append.append(response.html);
                $('#foodbakery-dev-cal-holder-' + _this_id).slideUp('fast');
                //this_act_msg.html(foodbakery_restaurant_strings.off_day_added);
            }
            this_loader.html('');
        }).fail(function () {
            this_loader.html('');
        });
    }
});

/**
 * search map
 */
function foodbakery_gl_search_map() {
    "use strict";
    var vals;
    vals = jQuery('#loc_address').val();
    jQuery('.gllpSearchField').val(vals);
    jQuery('#profile_form').prop('disabled', true);

    console.log('foodbakery_gl_search_map backend');

}

jQuery('#foodbakery_restaurant_type_icon_image').change(function () {
    var val = jQuery("select#foodbakery_restaurant_type_icon_image option").filter(":selected").val();
    if (val == 'image') {
        jQuery('#restaurant-type-icon-holder').hide();
        jQuery('#restaurant-type-image-holder').show(500);
    } else {
        jQuery('#restaurant-type-image-holder').hide();
        jQuery('#restaurant-type-icon-holder').show(500);
    }
});

jQuery('#foodbakery_restaurant_menu_type_icon_image').change(function () {
    var val = jQuery("select#foodbakery_restaurant_menu_type_icon_image option").filter(":selected").val();
    if (val == 'image') {
        jQuery('#restaurant-menu-type-icon-holder1').hide();
        jQuery('#restaurant-menu-type-image-holder1').show(500);
    } else {
        jQuery('#restaurant-menu-type-image-holder1').hide();
        jQuery('#restaurant-menu-type-icon-holder1').show(500);
    }
});

// (function( $ ) {
// $('.backup_locations_generates_area').on('click', '#btn_delete_locations_backup', function () {
// "use strict";
// var var_confirm = window.confirm("This action will delete your selected Backup File. Do you still want to continue?");
// if ( var_confirm == true ) {
// $(".outerwrapp-layer,.loading_div").fadeIn(100);

// var admin_url = $('.backup_generates_area').data('ajaxurl');
// var file_name = $(this).data('file');

// var dataString = 'file_name=' + file_name + '&action=delete_locations_backup_file';
// $.ajax({
// type: "POST",
// url: admin_url,
// data: dataString,
// success: function (response) {

// $(".loading_div").hide();
// $(".form-msg .innermsg").html(response);
// $(".form-msg").show();
// $(".outerwrapp-layer").delay(2000).fadeOut(100);
// window.location.reload(true);
// slideout();
// }
// });
// //return false;
// }
// });

// $('.backup_locations_generates_area').on('click', '#btn_restore_locations_backup, #btn_import_locations_from_url', function () {
// "use strict";
// $(".outerwrapp-layer,.loading_div").fadeIn(100);

// var admin_url = $('.backup_generates_area').data('ajaxurl');
// var file_name = $(this).data('file');

// var dataString = 'file_name=' + file_name + '&action=restore_locations_backup';

// if (typeof (file_name) === 'undefined') {

// var file_name = $('#bkup_locations_import_url').val();

// var dataString = 'file_name=' + file_name + '&file_path=yes&action=restore_locations_backup';
// }

// $.ajax({
// type: "POST",
// url: admin_url,
// data: dataString,
// success: function (response) {
// $(".loading_div").hide();
// $(".form-msg .innermsg").html(response);
// $(".form-msg").show();
// $(".outerwrapp-layer").delay(2000).fadeOut(100);
// window.location.reload(true);
// slideout();
// }
// });
// });
// })(jQuery);

function set_locations_backup_filename(file_value, file_path) {
    "use strict";
    jQuery(".backup_locations_generates_area .backup_action_btns").find('input[type="button"]').attr('data-file', file_value);
    jQuery(".backup_locations_generates_area .backup_action_btns").find('> a').attr('href', file_path + file_value);
    jQuery(".backup_locations_generates_area .backup_action_btns").find('> a').attr('download', file_value);
}

function generate_locations_backup(admin_url) {
    "use strict";
    jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);

    var dataString = 'action=generate_locations_backup';
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery(".loading_div").hide();
            jQuery(".form-msg .innermsg").html(response);
            jQuery(".form-msg").show();
            jQuery(".outerwrapp-layer").delay(100).fadeOut(100);
            //window.location.reload(true);
            slideout();
        }
    });
    //return false;
}

function set_restaurant_type_categories_backup_filename(file_value, file_path) {
    "use strict";
    jQuery(".backup_restaurant_type_categories_generates_area .backup_action_btns").find('input[type="button"]').attr('data-file', file_value);
    jQuery(".backup_restaurant_type_categories_generates_area .backup_action_btns").find('> a').attr('href', file_path + file_value);
    jQuery(".backup_restaurant_type_categories_generates_area .backup_action_btns").find('> a').attr('download', file_value);
}

function generate_restaurant_type_categories_backup(admin_url) {
    "use strict";
    jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);

    var dataString = 'action=generate_restaurant_type_categories_backup';
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery(".loading_div").hide();
            jQuery(".form-msg .innermsg").html(response);
            jQuery(".form-msg").show();
            jQuery(".outerwrapp-layer").delay(100).fadeOut(100);
            window.location.reload(true);
            slideout();
        }
    });
    //return false;
}

function foodbakery_ft_icon_feature(id) {//begin function

    var getting_icon;

    var ajax_url = foodbakery_pt_vars.ajax_url;

    var this_loader = $('#icon-' + id);
    this_loader.html('<div class="loader-holder" style="width:18px;"><img src="' + foodbakery_pt_vars.plugin_url + 'assets/backend/images/ajax-loader.gif" alt=""></div>');
    getting_icon = $.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            field: 'icon',
            action: 'foodbakery_ft_iconpicker'
        },
        dataType: "json"
    }).done(function (response) {
        if (typeof response.icon !== 'undefined') {
            this_loader.html(response.icon);
        }
    }).fail(function () {
        this_loader.html('');
    });

}//end function

/* Time Open Close Function Start */
jQuery(".time-list #close-btn2").click(function () {
    jQuery(".time-list .open-close-time").addClass('opening-time');
});
jQuery(".time-list #close-btn1").click(function () {
    jQuery(".time-list .open-close-time").removeClass('opening-time');
});

jQuery(document).on('click', 'a[id^="foodbakery-dev-open-time"]', function () {
    var _this_id = jQuery(this).data('id'),
            _this_day = jQuery(this).data('day'),
            _this_con = jQuery('#open-close-con-' + _this_day + '-' + _this_id),
            _this_status = jQuery('#foodbakery-dev-open-day-' + _this_day + '-' + _this_id);
    if (typeof _this_id !== 'undefined' && typeof _this_day !== 'undefined') {
        _this_status.val('on');
        _this_con.addClass('opening-time');
    }
});

jQuery(document).on('click', 'a[id^="foodbakery-dev-close-time"]', function () {
    var _this_id = jQuery(this).data('id'),
            _this_day = jQuery(this).data('day'),
            _this_con = jQuery('#open-close-con-' + _this_day + '-' + _this_id),
            _this_status = jQuery('#foodbakery-dev-open-day-' + _this_day + '-' + _this_id);
    if (typeof _this_id !== 'undefined' && typeof _this_day !== 'undefined') {
        _this_status.val('');
        _this_con.removeClass('opening-time');
    }
});

function foodbakery_show_company_users(value, ajax_url, plugin_url) {
    "use strict";
    var selecting_users,
            this_loader = $('#restaurant_user_publisher_col');
    this_loader.html('<div class="loader-holder"><img src="' + plugin_url + 'assets/backend/images/ajax-loader.gif" alt=""></div>');
    selecting_users = $.ajax({
        url: ajax_url,
        method: "POST",
        data: 'company=' + value + '&action=foodbakery_restaurant_back_publishers',
        dataType: "json"
    }).done(function (response) {
        if (typeof response.html !== 'undefined') {
            this_loader.html(response.html);
        }
        chosen_selectionbox();
    }).fail(function () {
        this_loader.html('');
    });
}

/*
 * Company Name based on Profile Type
 */

jQuery(document).on("change", ".publisher_profile_type", function () {
    current_val = jQuery(this).val();
    if (current_val == 'restaurant') {
        jQuery(".publisher_company_name").show();
    } else {
        jQuery(".publisher_company_name").hide();
    }
});

/*
 *  getting startd button hide show fields
 */

function foodbakery_getting_startrd() {
    if (jQuery("#foodbakery_header_buton_switch").val() == 'on') {
        jQuery("#header_button_title").show();
        jQuery("#header_button_url").show();
        jQuery("#foodbakery_head_btn").show();
    } else {
        jQuery("#foodbakery_head_btn").hide();
        jQuery("#header_button_title").hide();
        jQuery("#header_button_url").hide();
    }
}

/*
 *  header button locations on/off
 */

function foodbakery_default_location_check() {
    if (jQuery("#foodbakery_hedaer_location_switch").val() == 'on') {
        jQuery("#foodbakery_head_location").show();
    } else {
        jQuery("#foodbakery_head_location").hide();
    }
}
/*
 *  header button Restaurent type on/off
 */
function foodbakery_header_restaurent_type() {
    if (jQuery("#foodbakery_hedaer_restaurant_switch").val() == 'on') {
        jQuery("#foodbakery_head_restaurent_type").show();
    } else {
        jQuery("#foodbakery_head_restaurent_type").hide();
    }
}




/*
 * Social Auto Post ( plugin settings ) message format hide show..
 */
function foodbakery_autopost_twitter_hide_show(opt_id) {
    if (jQuery("#" + opt_id).val() != 'on') {
        jQuery("#twitter_message_format").hide();
    } else {
        jQuery("#twitter_message_format").show();
    }
}
function foodbakery_autopost_facebook_hide_show(opt_id) {
    if (jQuery("#" + opt_id).val() != 'on') {
        jQuery("#facebook_message_format").hide();
    } else {
        jQuery("#facebook_message_format").show();
    }
}

function foodbakery_autopost_linkedin_hide_show(opt_id) {
    if (jQuery("#" + opt_id).val() != 'on') {
        jQuery("#linkedin_message_format").hide();
    } else {
        jQuery("#linkedin_message_format").show();
    }
}
/*
 * End Social Auto Post ...
 */

var counter_banner = 0;
function  foodbakery_banner_add_banner(admin_url) {
    counter_banner++;
    var image_path = jQuery('#foodbakery_banner_field_image_rand').val();

    var banner_title_input = jQuery("#banner_title_input").val();
    var banner_style_input = jQuery("#banner_style_input").val();
    var banner_type_input = jQuery("#banner_type_input").val();
    var banner_field_url_input = jQuery("#banner_field_url_input").val();
    var banner_target_input = jQuery("#banner_target_input").val();
    var adsense_code_input = jQuery("#adsense_code_input").val();

    if (banner_style_input != "") {

        var dataString = 'image_path=' + image_path +
                '&banner_title_input=' + banner_title_input +
                '&banner_style_input=' + banner_style_input +
                '&banner_type_input=' + banner_type_input +
                '&banner_field_url_input=' + banner_field_url_input +
                '&banner_target_input=' + banner_target_input +
                '&counter_banner=' + counter_banner +
                '&adsense_code_input=' + adsense_code_input +
                '&action=foodbakery_banner_ads_banner';
        jQuery.ajax({
            type: "POST",
            url: admin_url,
            data: dataString,
            success: function (response) {
                jQuery("#banner_area").append(response);
                jQuery(".social-area").show(200);
                jQuery("#foodbakery_banner_field_image_rand,#banner_title_input,#banner_field_url_input,#adsense_code_input").val("");
                jQuery("#foodbakery_banner_field_image_rand_box").find('img').attr('src', '');
                jQuery("#banner_style_input").val("image");
                jQuery("#foodbakery_banner_field_image_rand_box").hide();//use this to hide image box and display only Browse button for adding next banner.
            }
        });
        //return false;
    }
}

function foodbakery_banner_toggle(id) {
    jQuery("#" + id).slideToggle("slow");
}

function foodbakery_banner_type_toggle(type, id) {
    if (type == 'image') {
        jQuery("#ads_image" + id).show();
        jQuery("#ads_code" + id).hide();
    } else if (type == 'code') {
        jQuery("#ads_image" + id).hide();
        jQuery("#ads_code" + id).show();
    }
}


/*
 * Validations
 */

jQuery("form").on("submit", function () {
    returnType = foodbakery_validation_process(jQuery(this), false);
    if (returnType == false) {
        return false;
    }
});

/*
 * Validation Process by Form
 */
function foodbakery_validation_process(form_name, display_popup) {
    var has_empty = false;
    var alert_messages = new Array();
    var field_empty = new Array();
    var object_array = new Array();
    jQuery(form_name).find('.foodbakery-dev-req-field-admin,.foodbakery-number-field,.foodbakery-email-field,.foodbakery-url-field,.foodbakery-date-field,.foodbakery-range-field').each(function (index_no) {
        is_visible = true;
        var thisObj = jQuery(this);
        var visible_id = thisObj.data('visible');
        field_empty[index_no] = false;
        /*
         * Remove validation from tab
         */

        var tab_id = thisObj.closest('.foodbakery_tab_block').attr('id');
        if (foodbakery_is_field(tab_id) == true) {
            jQuery('a[name="#' + tab_id + '"]').removeClass('foodbakery_tab_error');
        }

        if (foodbakery_is_field(visible_id) == true) {
            is_visible = jQuery("#" + visible_id).is(':hidden');
            if (jQuery("#" + visible_id).css('display') !== 'none') {
                is_visible = true;
            } else {
                is_visible = false;
            }
        }
        if (thisObj.attr('type') == 'checkbox') {
            thisObj = jQuery("#" + thisObj.attr('name'));
            if (thisObj.val() == 'off') {
                thisObj.val('');
            }
        }
        if (!thisObj.val() && is_visible == true) {
            if (thisObj.hasClass('foodbakery-dev-req-field-admin')) {
                array_length = alert_messages.length;
                alert_messages[array_length] = foodbakery_insert_error_message(thisObj, alert_messages, 'is required!');
                object_array[array_length] = thisObj;
                has_empty = true;
                field_empty[index_no] = true;
            }
        } else {
            if (is_visible == true) {
                has_empty = foodbakery_check_field_type(thisObj, alert_messages, has_empty);
                array_length = alert_messages.length;
                field_empty[index_no] = foodbakery_check_field_type(thisObj, alert_messages, field_empty[index_no]);
                if (field_empty[index_no] == true) {
                    object_array[array_length] = thisObj;
                }
                if (thisObj.hasClass('meta-field-error') == true) {
                    object_array[array_length] = thisObj;
                    alert_messages[array_length] = 'Meta key is not valid';
                    field_empty[index_no] = true;
                    has_empty = true;
                }
            }
        }
        if (field_empty[index_no] == true) {
            if (thisObj.is(':visible') == false) {
                thisObj.closest(jQuery('.pbwp-form-holder[style="display:none;"]')).css('display', 'block');
                thisObj.closest(jQuery('.pbwp-form-holder[style="display: none;"]')).css('display', 'block');
            }
        }

        if (field_empty[index_no] == false) {
            thisObj.next('.chosen-container').removeClass('admin-field-error');
            thisObj.next('.foodbakery-dev-req-field-admin').next('.pbwp-box').removeClass('admin-field-error');
            thisObj.removeClass('admin-field-error');
        }

    });
    if (has_empty) {
        array_length = alert_messages.length;
        error_data = '<h3>Please fill out below fields correctly before submitting form.</h3><ul class="foodbakery-form-validations">';
        for (i = 0; i <= array_length; i++) {
            var thisObject = object_array[i];
            if (foodbakery_is_field(thisObject) == true) {
                var tab_id = thisObject.closest('.foodbakery_tab_block').attr('id');
                if (foodbakery_is_field(tab_id) == true) {
                    jQuery('a[name="#' + tab_id + '"]').addClass('foodbakery_tab_error');
                }
            }
            error_data += '<li>' + alert_messages[i] + '</li>';
        }
        error_data += '</ul>';
        //if (display_popup != false) {
        jQuery(".foodbakery-error-messages").html('<h4>Please ensure that all required fields are completed and formatted correctly.</h4>');
        jQuery(".foodbakery-error-messages").show();

        setTimeout(function () {
            jQuery(".foodbakery-error-messages").hide();
        }, 5000);
        //}
        return false;
    }
}

/*
 * Check if field exists and not empty
 */

function foodbakery_is_field(field_value) {
    if (field_value != 'undefined' && field_value != undefined && field_value != '') {
        return true;
    } else {
        return false;
    }
}

/*
 * Check if Provided data for field is valid
 */

function foodbakery_check_field_type(thisObj, alert_messages, has_empty) {
    /*
     * Check for Email Field
     */
    if (thisObj.hasClass('foodbakery-email-field')) {
        var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
        if (!pattern.test(thisObj.val())) {
            array_length = alert_messages.length;
            alert_messages[array_length] = foodbakery_insert_error_message(thisObj, alert_messages, 'is not valid Email!');
            has_empty = true;
        }
    }

    /*
     * Check for Number Field
     */

    if (thisObj.hasClass('foodbakery-number-field')) {
        var pattern = /[0-9 -()+]+$/;
        if (!pattern.test(thisObj.val())) {
            array_length = alert_messages.length;
            alert_messages[array_length] = foodbakery_insert_error_message(thisObj, alert_messages, 'is not valid Number!');
            has_empty = true;
        }
    }

    /*
     * Check for URL Field
     */

    if (thisObj.hasClass('foodbakery-url-field')) {
        var pattern = /^(http|https)?:\/\/[a-zA-Z0-9-\.]+\.[a-z]{2,4}/;
        if (!pattern.test(thisObj.val())) {
            array_length = alert_messages.length;
            alert_messages[array_length] = foodbakery_insert_error_message(thisObj, alert_messages, 'is not valid URL!');
            has_empty = true;
        }
    }

    /*
     * Check for Date Field
     */

    if (thisObj.hasClass('foodbakery-date-field')) {
        //var pattern = /([0-9][1-2])\/([0-2][0-9]|[3][0-1])\/((19|20)[0-9]{2})/;
        var pattern = /^\d{1,2}.\d{1,2}.\d{4} \d{2}:\d{2}$/;
        if (!pattern.test(thisObj.val())) {
            array_length = alert_messages.length;
            alert_messages[array_length] = foodbakery_insert_error_message(thisObj, alert_messages, 'is not valid Date!');
            has_empty = true;
        }
    }

    /*
     * Check for Meta Field
     */

    if (thisObj.hasClass('dir_meta_key_field')) {
        if (thisObj.val().indexOf(' ') > -1) {
            array_length = alert_messages.length;
            alert_messages[array_length] = foodbakery_insert_error_message(thisObj, alert_messages, 'is not valid Meta!');
            has_empty = true;
        }
    }

    /*
     * Check for Range Field
     */

    if (thisObj.hasClass('foodbakery-range-field')) {
        var min_val = thisObj.data('min');
        var max_val = thisObj.data('max');
        if (!(thisObj.val() >= min_val) || !(thisObj.val() <= max_val)) {
            array_length = alert_messages.length;
            alert_messages[array_length] = foodbakery_insert_error_message(thisObj, alert_messages, 'is not in Range! ( ' + min_val + ' - ' + max_val + ' )');
            has_empty = true;
        }
    }
    return has_empty;
}

/*
 * Making list of errors
 */

function foodbakery_insert_error_message(thisObj, alert_messages, error_msg) {
    var tab_title = thisObj.closest('.foodbakery_tab_block').data('title');
    if (foodbakery_is_field(tab_title) == true) {
        tab_title = tab_title + ' : ';
    } else {
        tab_title = '';
    }
    thisObj.addClass('admin-field-error');
    if (thisObj.is('select')) {
        thisObj.next('.chosen-container').addClass('admin-field-error');
    }
    if (thisObj.is(':hidden')) {
        thisObj.next('.foodbakery-dev-req-field-admin').next('.pbwp-box').addClass('admin-field-error');
    }
    var field_label = thisObj.closest('.form-elements').children('div').children('label').html();
    return '<strong>' + tab_title + '</strong>' + field_label + ' field ' + error_msg;
}




/*
 * upload file location
 */

$('#btn_import_file').click(function (event) {
    form_data = new FormData(jQuery("#foodbakery_import_form")[0]);
    form_data.append('action', 'foodbakery_uploading_import_file');
    $.ajax({
        type: "POST",
        url: foodbakery_globals.ajax_url,
        data: form_data,
        contentType: false,
        processData: false,
        success: function (response) {
            var file_url = response;
            var dataString = 'file_name=' + file_url + '&file_path=yes&action=restore_locations_backup';
            $.ajax({
                type: "POST",
                url: foodbakery_globals.ajax_url,
                data: dataString,
                success: function (response) {
                    //console.log(response);
                    $(".loading_div").hide();
                    $(".form-msg .innermsg").html(response);
                    $(".form-msg").show();
                    $(".outerwrapp-layer").delay(2000).fadeOut(100);
                    window.location.reload(true);
                    slideout();
                }
            });

        }

    });
});
/*
 * end upload file location
 */


/*
 * upload category file
 */

$('#btn_import_cat_file').click(function (event) {
    form_data = new FormData(jQuery("#foodbakery_import_categort_form")[0]);
    form_data.append('action', 'foodbakery_uploading_import_cat_file');
    $.ajax({
        type: "POST",
        url: foodbakery_globals.ajax_url,
        data: form_data,
        contentType: false,
        processData: false,
        success: function (response) {
            var file_url = response;
            var dataString = 'file_name=' + file_url + '&file_path=yes&action=restore_restaurant_type_categories_backup';
            $.ajax({
                type: "POST",
                url: foodbakery_globals.ajax_url,
                data: dataString,
                success: function (response) {
                    //console.log(response);
                    $(".loading_div").hide();
                    $(".form-msg .innermsg").html(response);
                    $(".form-msg").show();
                    $(".outerwrapp-layer").delay(2000).fadeOut(100);
                    window.location.reload(true);
                    slideout();
                }
            });

        }

    });
});
/*
 * end upload category file
 */


jQuery(document).on("change", ".base-currency-change", function () {
    var base_currency = jQuery(this).val();
    var dataString = 'base_currency=' + base_currency + '&action=curriencies_list_based_currency';
    $.ajax({
        type: "POST",
        url: foodbakery_globals.ajax_url,
        data: dataString,
        success: function (response) {
            jQuery("#foodbakery_currency_id").html(response);
            jQuery('#foodbakery_currency_id').trigger("chosen:updated");
        }
    });
});


function foodbakery_pl_opt_backup_generate(admin_url) {
    "use strict";
    jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);

    var dataString = 'action=foodbakery_pl_opt_backup_generate';
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            //console.log(response);
            jQuery(".loading_div").hide();
            jQuery(".form-msg .innermsg").html(response);
            jQuery(".form-msg").show();
            jQuery(".outerwrapp-layer").delay(100).fadeOut(100);
            window.location.reload(true);
            slideout();
        }
    });
    //return false;
}

function foodbakery_set_p_filename(file_value, file_path) {
    "use strict";
    jQuery(".backup_action_btns").find('input[type="button"]').attr('data-file', file_value);
    jQuery(".backup_action_btns").find('> a').attr('href', file_path + file_value);
    jQuery(".backup_action_btns").find('> a').attr('download', file_value);
}

jQuery('.backup_generates_area').on('click', '#cs-p-backup-restore, #cs-p-backup-url-restore', function () {
    "use strict";

    jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);

    var admin_url = jQuery('.backup_generates_area').data('ajaxurl');
    var file_name = jQuery(this).data('file');

    var dataString = 'file_name=' + file_name + '&action=foodbakery_pl_backup_file_restore';

    if (typeof (file_name) === 'undefined') {

        var file_name = jQuery('#bkup_import_url').val();

        var dataString = 'file_name=' + file_name + '&file_path=yes&action=foodbakery_pl_backup_file_restore';
    }

    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {

            jQuery(".loading_div").hide();
            jQuery(".form-msg .innermsg").html(response);
            jQuery(".form-msg").show();
            jQuery(".outerwrapp-layer").delay(2000).fadeOut(100);


            window.location.reload(true);
            slideout();
        }
    });
    //return false;
});

if ($(".restaurant-nutri-icons-list").length != '') {

    $('.restaurant-nutri-icons-list').sortable({
        handle: '.drag-option',
        cursor: 'move'
    });
}

function foodbakery_add_nutri_icon(nutri_item_counter) {
    jQuery('#add-nutri-icon-from-' + nutri_item_counter).slideToggle();
}

function foodbakery_remove_nutri_item(nutri_item_counter) {
    jQuery('.nutri-item-' + nutri_item_counter).remove();
}

function foodbakery_close_nutri_icon(nutri_item_counter) {
    jQuery('#add-nutri-icon-from-' + nutri_item_counter).slideUp();
}

function foodbakery_admin_add_nutri_icon_to_list(nutri_item_counter) {

    var nutri_icon_title = $('#nutri_item_title_' + nutri_item_counter);
    var nutri_icon_img = $('#foodbakery_nutri_item_img_' + nutri_item_counter + '_rand');
    var nutri_icon_img_box = $('#foodbakery_nutri_item_img_' + nutri_item_counter + '_rand_box');
    var this_loader = $('#nutri-icons-loader-' + nutri_item_counter);
    var this_append = $('#restaurant-cats-list-' + nutri_item_counter);

    if (nutri_icon_title.val() != '') {
        $.ajax({
            url: foodbakery_globals.ajax_url,
            method: "POST",
            data: '_nutri_icon_title=' + nutri_icon_title.val() + '&_nutri_icon_img=' + nutri_icon_img.val() + '&action=restaurant_add_nutri_icon_item',
            dataType: "json"
        }).done(function (response) {
            this_append.append(response.html);
            nutri_icon_title.val('');
            nutri_icon_img.val('');
            nutri_icon_img_box.hide();
            this_loader.html('');
        }).fail(function () {
            this_loader.html('');
        });
    } else {
        alert('Please fill the required * fields.');
    }
}

var default_loader = jQuery(".foodbakery_loader").html();
var default_button_loader = jQuery(".foodbakery-button-loader").html();
/*
 * Loader Show Function
 */
function foodbakery_show_loader(loading_element, loader_data, loader_style, thisObj) {
    var loader_div = '.foodbakery_loader';
    if (loader_style == 'button_loader') {
        loader_div = '.foodbakery-button-loader';
        if (thisObj != 'undefined' && thisObj != '') {
            thisObj.addClass('foodbakery-processing');
        }

    }
    if (typeof loader_data !== 'undefined' && loader_data != '' && typeof jQuery(loader_div) !== 'undefined') {
        jQuery(loader_div).html(loader_data);
    }
    if (typeof loading_element !== 'undefined' && loading_element != '' && typeof jQuery(loader_div) !== 'undefined') {
        jQuery(loader_div).appendTo(loading_element);
    }
    jQuery(loader_div).show();
}
function foodbakery_hide_button_loader(processing_div) {
    if (processing_div != 'undefined' && processing_div != '' && processing_div != undefined) {
        jQuery(processing_div).removeClass('foodbakery-processing');
    }
    jQuery(".foodbakery-button-loader").hide();
    jQuery(".foodbakery-button-loader").html(default_button_loader);
}

function foodbakery_load_all_publishers( field_class, selected_publisher ){
    jQuery('.'+ field_class + ' .select-loader' ).html("<img src='" + foodbakery_globals.plugin_url + "/assets/backend/images/ajax-loader.gif' />").show();
    jQuery.ajax({
        type: "POST",
        url: foodbakery_globals.ajax_url,
        data: 'action=foodbakery_load_all_publishers&selected_publisher='+ selected_publisher,
        dataType: "json",
        success: function (response) {
            if (typeof response.html !== 'undefined') {
                jQuery('.'+ field_class).prop("onclick", null);
                jQuery('.'+ field_class).html('');
                jQuery('.'+ field_class).html(response.html);
                jQuery('.'+ field_class + ' .select-loader' ).html('').hide();
                setTimeout(function() {
                    jQuery('.'+ field_class + ' #foodbakery_restaurant_publisher').trigger('chosen:open');
                }, 5);
            }
        }
    });
}

function foodbakery_load_dropdown_values( field_class, field_id, action ){
    jQuery('.'+ field_class + ' .select-loader' ).html("<img src='" + foodbakery_globals.plugin_url + "/assets/backend/images/ajax-loader.gif' />").show();
    var selected_val = jQuery('#foodbakery_'+ field_id).val();
    jQuery.ajax({
        type: "POST",
        url: foodbakery_globals.ajax_url,
        data: 'action='+ action +'&selected_val='+ selected_val,
        dataType: "json",
        success: function (response) {
            if (typeof response.html !== 'undefined') {
                jQuery('.'+ field_class).prop("onclick", null);
                jQuery('.'+ field_class).html('');
                jQuery('.'+ field_class).html(response.html);
                jQuery('.'+ field_class + ' .select-loader' ).html('').hide();
                setTimeout(function() {
                    jQuery('.'+ field_class + ' #foodbakery_'+ field_id).trigger('chosen:open');
                }, 5);
            }
        }
    });
}

function foodbakery_load_all_pages( field_id, args ){
    jQuery('.pages-loader-holder .loader-'+ field_id ).html("<img src='" + foodbakery_globals.plugin_url + "/assets/backend/images/ajax-loader.gif' />").show();
    var args = jQuery('.pages-loader-holder .args_'+ field_id).text();
    jQuery.ajax({
        type: "POST",
        url: foodbakery_globals.ajax_url,
        data: 'action=foodbakery_load_all_pages&args='+ args + '&field_id='+ field_id,
        dataType: "json",
        success: function (response) {
            if (typeof response.html !== 'undefined') {
                jQuery('.pages-loader-holder #'+ field_id +'_holder').html('');
                jQuery('.pages-loader-holder #'+ field_id +'_holder').html(response.html);
                jQuery('.pages-loader-holder .loader-'+ field_id ).html('').hide();
                setTimeout(function() {
                    jQuery('.pages-loader-holder #'+ field_id).trigger('chosen:open');
                }, 5);
            }
        }
    });
}

jQuery(document).on("change", ".review_order_change", function () {
    var post_id =  jQuery(this).data('id');
    var restaurant_id =  jQuery(this).data('restaurant_id');
    var review_status_val =  jQuery(this).val();
    /*Loader show*/
    jQuery('#foodbakery_loader_'+post_id).show();
    jQuery.post(ajaxurl, {'action' : 'update_reviews_status','post_id' : post_id, 'status' : review_status_val, 'restaurant_id' : restaurant_id}, function (res) {
        var result = JSON.parse(res);
        /*Loader hide*/
        jQuery('#foodbakery_loader_'+post_id).hide();
    });
});