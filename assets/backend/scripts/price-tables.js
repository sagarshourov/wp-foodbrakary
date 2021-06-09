$(document).ready(function ($) {
    var row_num_input = "<input type='hidden' class='row_num_input' name='dir_pt_row_num[]'>";
    var pt_col_input = "<input type='text' name='dir_pt_col_val[]' size='10' value='Title'>";
    var pt_sec_input = "<input type='text' name='dir_pt_sec_val[]' value='Section'>";

    $(".dir-add-column").on("click", function () {//begin function

        var _this_id = $(this).data('id');

        //appending package default fields while adding new column (Membership)
        foodbakery_pt_package_fixed_fields();

        //append the new column
        $("#foodbakery-price-table-" + _this_id).find('tr.pt_row').each(function () {
            var rand_id = Math.floor((Math.random() * 99999999) + 1);
            $(this).append('<td>' + pt_col_input + '<br><span id="icon-' + rand_id + '"></span></td>');

            //icon loader
            foodbakery_pt_icon(rand_id);
        });

        //appending row_num_input
        $("#foodbakery-price-table-" + _this_id + " tr.pt_row td:first").find('input.row_num_input').remove();
        $("#foodbakery-price-table-" + _this_id + " tr.pt_row td:first").append(row_num_input);

        //setting section col span
        var dir_num_cols = $("#foodbakery-price-table-" + _this_id + " tr.default-fields:first td").length;
        $("#foodbakery-price-table-" + _this_id + " tr.pt_section td:nth-child(2)").attr('colspan', (dir_num_cols -1));

        //appending actions
        foodbakery_action_cols(_this_id, 'column');

    });//end function

    $(".dir-add-row").on("click", function () {//begin function

        var _this_id = $(this).data('id');

        //number of td elements in the first row
        var dir_num_cols = $("#foodbakery-price-table-" + _this_id + " tr.default-fields:first td").length;

        if (dir_num_cols > 1) {

            //append the new table row
            $("#foodbakery-price-table-" + _this_id + " tbody").append("\n<tr class='pt_row'>\n" + foodbakery_num_of_cols(dir_num_cols - 1) + "\n\n</tr>\n");

            //appending actions
            foodbakery_action_cols(_this_id, 'row');

            //adding title field
            $("#foodbakery-price-table-" + _this_id + " tbody tr:last td:first").append('<br><input type="text" name="dir_pt_row_title[]" value="Row Title">');

            //icon loader
            $("#foodbakery-price-table-" + _this_id + " tbody tr:last span[id^=\"icon-\"]").each(function () {
                var rand_id = $(this).data('id');
                foodbakery_pt_icon(rand_id);
            });

        } else {
            alert('Add a Membership first.')
        }

    });//end function

    $(".dir-add-section").on("click", function () {//begin function

        var _this_id = $(this).data('id');

        //number of td elements in the first row
        var dir_num_cols = $("#foodbakery-price-table-" + _this_id + " tr.default-fields:first td").length;

        if (dir_num_cols > 1) {
            //setting section position
            var pt_sec_pos_val = $("#foodbakery-price-table-" + _this_id + " .row_num_input").length;
            var pt_sec_pos = "<input type='hidden' name='dir_pt_sec_pos[]' value='" + pt_sec_pos_val + "'>";

            //append the new table row    
            $("#foodbakery-price-table-" + _this_id + " tbody").append("\n<tr class='pt_section'>\n" + "<td colspan='" + dir_num_cols + "'>" + pt_sec_input + pt_sec_pos + "</td>" + "\n\n</tr>\n");

            //appending actions
            foodbakery_action_cols(_this_id, 'section');
        } else {
            alert('Add a Membership first.')
        }

    });//end function

    $(".dir-get-html").on("click", function () {//begin function

        var _this_id = $(this).data('id');
        alert($("#foodbakery-price-table-" + _this_id)[0].outerHTML);

    });//end function

    /*
     this function will return a string holding td elements.
     The number of elements returned is based on the dir_num_cols
     value passed in.
     */

    function foodbakery_num_of_cols(dir_num_cols) {//begin function

        //array to hold the td elements
        var cols = [];

        //loop through the dir_num_cols
        for (var i = 0; i < dir_num_cols; i++) {//begin for loop

            var rand_id = Math.floor((Math.random() * 99999999) + 1);

            var dir_row_nums;
            if (i == 0) {
                dir_row_nums = row_num_input;
            } else {
                dir_row_nums = "";
            }
            //add a td element to the cols array
            cols.push("<td>" + pt_col_input + '<br><span id="icon-' + rand_id + '" data-id="' + rand_id + '"></span>' + dir_row_nums + "</td>");

        }//end for loop

        //return the cols array as a string
        return cols.join("");

    }//end function

    /*
     Embed action cols in table
     */

    function foodbakery_action_cols(id, part) {//begin function

        var table = $("#foodbakery-price-table-" + id);
        //embeding rows actions
        if (part == 'row') {
            table.find('tr.pt_row:last').find('td:first').before('<td class="pt_row_actions"><a class="pt_delete_row">x</a></td>');
        }
        if (part == 'section') {
            table.find('tr.pt_section:last').find('td:first').before('<td class="pt_row_actions"><a class="pt_delete_row">x</a></td>');
        }

        //embeding cols actions
        //number of td elements in the first row
        var cols = [];
        var dir_num_cols = $("#foodbakery-price-table-" + id + " tr.default-fields:first td").length;
        var pt_col_joins;
        //loop through the dir_num_cols
        for (var i = 0; i < dir_num_cols; i++) {//begin for loop

            var pt_col_actions;
            if (i == 0) {
                pt_col_actions = '<td>&nbsp;</td>';
            } else {
                pt_col_actions = '<td class="pt_col_actions"><a class="pt_delete_col">x</a></td>';
            }
            //add a td element to the cols array
            cols.push(pt_col_actions);

        }//end for loop

        pt_col_joins = cols.join("");

        if (pt_col_joins != '') {
            table.find('tr.actions_row').remove();
            table.find('tr:first').before('<tr class="actions_row">' + pt_col_joins + '</tr>');
        }

    }//end function

    /*
     Delete column of table
     */

    $(document).on("click", ".pt_delete_col", function () {//begin function
        var _this = $(this);
        var _table = _this.parents('table');
        var _this_index = _this.parent('td').index();
        _table.find('tr td:nth-child(' + (_this_index + 1) + ')').remove();

        _table.find('tr').each(function () {
            if ($(this).find('td').length <= 1) {
                $(this).remove();
            }
        });

        if (_table.find('input').length == 0) {
            _table.html(foodbakery_pt_default_html());
        }
    });

    /*
     Delete row of table
     */

    $(document).on("click", ".pt_delete_row", function () {//begin function
        var _this = $(this);
        var _table = _this.parents('table');
        var _this_row = _this.parents('tr');
        var _this_row_index = _this_row.index();
        _this_row.remove();

        //re-assigning section positions
        if (!_this_row.hasClass('pt_section')) {
            _table.find('tr.pt_section').each(function () {
                var get_pos = $(this).find('input[name^="dir_pt_sec_pos"]');
                if (get_pos.attr('value') > 0 && _this_row_index <= $(this).index()) {
                    get_pos.attr('value', get_pos.val() - 1);
                }
            });
        }

        //removing table inner html
        //if no row left
        if (_table.find('input').length == 0) {
            _table.html(foodbakery_pt_default_html());
        }
    });

    /*
     Reset all the fields and add default fields.
     */

    $(".dir-cols-reset").on("click", function () {//begin function

        var _this_id = $(this).data('id');

        var default_data = foodbakery_pt_default_html();

        var r = confirm("Are you sure! This will remove all data.");
        if (r == true) {
            $("#foodbakery-price-table-" + _this_id).html(default_data);
        } else {
            return false;
        }

    });//end function

    function foodbakery_pt_icon(id) {//begin function

        var getting_icon;
        var ajax_url = foodbakery_pt_vars.ajax_url;
        var this_loader = $('#icon-' + id);
        this_loader.html('<div class="loader-holder" style="width:18px;"><img src="' + foodbakery_pt_vars.plugin_url + 'assets/backend/images/ajax-loader.gif" alt=""></div>');
        getting_icon = $.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                field: 'icon',
                action: 'foodbakery_pt_iconpicker'
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

    /*
     Membership Fixed Fields
     */

    function foodbakery_pt_package_fixed_fields() {//begin function

        $('tr.default-fields').each(function () {
            if ($(this).index() == 1) {
                $(this).append('<td class="pkg-name"><input type="text" name="pt_pkg_name[]" value="Membership"></td>');
            } else if ($(this).index() == 2) {
                $(this).append('<td class="pkg-price"><input type="text" name="pt_pkg_price[]" value="20"></td>');
            } else if ($(this).index() == 3) {
                $(this).append('<td class="pkg-color"><input type="text" class="bg_color" name="pt_pkg_color[]" value="#fff"></td>');
		$('.bg_color').wpColorPicker();
            } else if ($(this).index() == 4) {
                $(this).append('<td class="pkg-desc"><input type="text" name="pt_pkg_desc[]" value=""></td>');
            } else if ($(this).index() == 5) {
                $(this).append('<td class="pkg-btn-txt"><input type="text" name="pt_pkg_btn_txt[]" value=""></td>');
            } else if ($(this).index() == 6) {
                $(this).append('<td class="pkg-duration"><input type="text" name="pt_pkg_dur[]" value=""></td>');
            } else if ($(this).index() == 7) {
                $(this).append('<td class="pkg-featured"><select name="pt_pkg_feat[]"><option value="no">No</option><option value="yes">Yes</option></select></td>');
            } else if ($(this).index() == 8) {
                $(this).append('<td class="pkg-url">' + foodbakery_pt_vars.packages_dropdown + '</td>');
            }
        });

    }//end function

    function foodbakery_pt_default_html() {
        var default_data = '\
        <tbody>\
            <tr class="actions_row">\
                <td>&nbsp;</td>\
            </tr>\
            <tr class="default-fields">\
                <td class="pkg-name">Name</td>\
            </tr>\
            <tr class="default-fields">\
                <td class="pkg-price">Price</td>\
            </tr>\
            <tr class="default-fields">\
                <td class="pkg-color">Color</td>\
            </tr>\
            <tr class="default-fields">\
                <td class="pkg-desc">Description</td>\
            </tr>\
            <tr class="default-fields">\
                <td class="pkg-btn-txt">Button Text</td>\
            </tr>\
            <tr class="default-fields">\
                <td class="pkg-duration">Duration</td>\
            </tr>\
            <tr class="default-fields">\
                <td class="pkg-featured">Featured</td>\
            </tr>\
            <tr class="default-fields">\
                <td class="pkg-url">Membership</td>\
            </tr>\
        </tbody>';

        return default_data;
    }
});