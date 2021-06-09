// restaurants top map

jQuery('form[name="foodbakery-top-map-form"]').keydown(function (event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        return false;
    }
});

jQuery(document).on('focusin', '.foodbakery-top-loc-wrap input', function () {
    var ajax_url = foodbakery_top_gmap_strings.ajax_url;
    var _plugin_url = foodbakery_top_gmap_strings.plugin_url;
    var _this = jQuery(this);
    if (jQuery(this).hasClass('foodbakery-dev-load-locs')) {
        var list_to_append = jQuery(this).parents('label').find(".top-search-locations");
        var this_loader = jQuery(this).parents('label').find('.loc-icon-holder');
        this_loader.html('<img src="' + _plugin_url + 'assets/frontend/images/ajax-loader.gif" alt="">');
        var _top_map_locs = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: 'locs=top_map&action=dropdown_options_for_search_location_data',
            dataType: "json"
        }).done(function (response) {
            if (response) {
                list_to_append.html('');
                jQuery.each(response, function () {
                    list_to_append.append("<li data-val=\'" + this.value + "\'>" + this.caption + "</li>");
                });
                _this.removeClass('foodbakery-dev-load-locs');
            }
            this_loader.html('<i class="icon-target3"></i>');
        }).fail(function () {
            this_loader.html('<i class="icon-target3"></i>');
        });
    }
    jQuery(this).parents('.foodbakery-top-loc-wrap').find('.top-search-locations').show();
});

jQuery(document).on('click', '.foodbakery-top-loc-wrap .top-search-locations > li', function () {
    var _this_data = jQuery(this).data('val');
    var locations_field = jQuery(this).parents('.foodbakery-top-loc-wrap').find('input');
    locations_field.val(_this_data);
    foodbakery_top_serach_trigger();
    jQuery(this).parents('.foodbakery-top-loc-wrap').find('.top-search-locations').hide();
});

jQuery(document).on('click', 'body', function (e) {
    var container = jQuery(".foodbakery-top-loc-wrap");

    if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.find('.top-search-locations').hide();
    }
});

function foodbakery_top_serach_trigger() {
    var ajax_url = foodbakery_top_gmap_strings.ajax_url;
    var _plugin_url = foodbakery_top_gmap_strings.plugin_url;
    var _this_form = jQuery('form[name="foodbakery-top-map-form"]');
    var this_loader = jQuery('.top-gmap-loader');
    this_loader.html('<div class="loader-holder"><img src="' + _plugin_url + 'assets/frontend/images/ajax-loader.gif" alt=""></div>');
    var loading_top_map = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: 'ajax_filter=true&map=top_map&action=foodbakery_top_map_search&' + _this_form.serialize(),
        dataType: "json"
    }).done(function (response) {
        if (typeof response.html !== 'undefined') {
            jQuery('.top-map-action-scr').html(response.html);
        }
        this_loader.html('');
    }).fail(function () {
        this_loader.html('');
    });
}

function foodbakery_restaurant_top_map(top_dataobj, is_ajax) {
    var map_zoom = top_dataobj.map_zoom,
            this_map_style = top_dataobj.map_style,
            latitude = top_dataobj.latitude,
            longitude = top_dataobj.longitude,
            db_cords = top_dataobj.map_cords,
            cluster_icon = top_dataobj.cluster_icon,
            cordsActualLimit = 1000;

    var open_info_window;

    if (latitude != '' && longitude != '') {

        var marker;
        all_marker = [];
        reset_top_map_marker = [];

        var LatLngList = [];

        if (is_ajax != 'true') {
            map_zoom = parseInt(map_zoom);
            if (!jQuery.isNumeric(map_zoom)) {
                var map_zoom = 9;
            }
            var map_type = google.maps.MapTypeId.ROADMAP;
            var mapLatlng = new google.maps.LatLng(latitude, longitude);
            map = new google.maps.Map(jQuery('.foodbakery-ontop-gmap').get(0), {
                zoom: map_zoom,
                center: mapLatlng,
                mapTypeControl: false,
                streetViewControl: false,
                mapTypeId: map_type,
            });
        }

        if (typeof this_map_style !== 'undefined' && this_map_style != '') {

            var styles = foodbakery_map_select_style(this_map_style);
            if (styles != '') {
                var styledMap = new google.maps.StyledMapType(styles, {name: 'Styled Map'});
                map.mapTypes.set('map_style', styledMap);
                map.setMapTypeId('map_style');
            }
        }

        // Showing all markers in default for page load

        if (typeof db_cords === 'object' && db_cords.length > 0) {
            var actual_length;
            if (db_cords.length > cordsActualLimit) {
                actual_length = cordsActualLimit;
            } else {
                actual_length = db_cords.length;
            }

            var def_cords_obj = [];
            var def_cords_creds = [];

            jQuery.each(db_cords, function (index, element) {

                if (index === actual_length) {
                    return false;
                }
                var i = index;
                //alert(element.title);
                //alert(element.marker);
                var db_lat = parseFloat(element.lat);
                var db_long = parseFloat(element.long);
                var list_title = element.title;
                var list_marker = element.marker;

                var def_cords = {lat: db_lat, lng: db_long};
                def_cords_obj.push(def_cords);

                var def_coroeds = {list_title: list_title, list_marker: list_marker, element: element};
                def_cords_creds.push(def_coroeds);

                var db_latLng = new google.maps.LatLng(db_lat, db_long);

                LatLngList.push(new google.maps.LatLng(db_lat, db_long));
                marker = new google.maps.Marker({
                    position: db_latLng,
                    center: db_latLng,
                    //animation: google.maps.Animation.DROP,
                    map: map,
                    draggable: false,
                    icon: list_marker,
                    title: list_title,
                });

                var contentString = infoContentString(element);

                var infowindow = new InfoBox({
                    boxClass: 'liting_map_info',
                    content: contentString,
                    disableAutoPan: true,
                    maxWidth: 0,
                    alignBottom: true,
                    pixelOffset: new google.maps.Size(-108, -72),
                    zIndex: null,
                    closeBoxMargin: "2px",
                    closeBoxURL: "close",
                    infoBoxClearance: new google.maps.Size(1, 1),
                    isHidden: false,
                    pane: "floatPane",
                    enableEventPropagation: false
                });

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        map.panTo(marker.getPosition());
                        map.panBy(0, -150);
                        if (open_info_window)
                            open_info_window.close();
                        infowindow.open(map, this);
                        open_info_window = infowindow;
                    }
                })(marker, i));
                all_marker.push(marker);
                reset_top_map_marker.push(marker);
            });

            if (LatLngList.length > 0) {
                var latlngbounds = new google.maps.LatLngBounds();
                for (var i = 0; i < LatLngList.length; i++) {
                    latlngbounds.extend(LatLngList[i]);
                }
                map.setCenter(latlngbounds.getCenter(), map.fitBounds(latlngbounds));

                map.setZoom(map.getZoom());

                var mapResizeTimes = 0;
                setTimeout(function () {
                    if (mapResizeTimes === 0) {
                        jQuery(".foodbakery-ontop-gmap").height(jQuery(window).height);
                        google.maps.event.trigger(map, "resize");
                    }
                    mapResizeTimes++;
                }, 500);
            }

            //clusters
            mapClusters();
            google.maps.event.addListener(map, "click", function (event) {
                open_info_window.close();
            });
        }
        //        

        function mapClusters() {
            if (all_marker) {
                var mcOptions;
                var clusterStyles = [
                    {
                        textColor: '#222222',
                        opt_textColor: '#222222',
                        url: cluster_icon,
                        height: 40,
                        width: 40,
                        textSize: 12
                    }
                ];
                mcOptions = {
                    gridSize: 15,
                    ignoreHidden: true,
                    maxZoom: 12,
                    styles: clusterStyles
                };
                markerClusterers = new MarkerClusterer(map, all_marker, mcOptions);
            }
        }

        function infoContentString(element) {
            var restaurant_id = element.id;
            var list_title = element.title;
            var list_link = element.link;
            var list_img = element.img;
            var list_price = element.price;
            var list_shortlist = element.shortlist;
            var list_featured = element.featured;
            var list_reviews = element.reviews;
            //var list_address = element.address;

            var img_html = '';
            if (list_img !== 'undefined' && list_img != '') {
                img_html = '<figure>' + list_img + '</figure>';
            }

            var contentString = '\
            <div id="restaurant-info-' + restaurant_id + '-' + '" class="restaurant-info-inner">\
                <div class="info-main-container">\
                    ' + img_html + '\
                    <div class="info-txt-holder">\
                        ' + list_featured + '\
                        ' + list_reviews + '\
                        ' + list_shortlist + '\
                        <a class="info-title" href="' + list_link + '">' + list_title + '</a>\
                        ' + list_price + '\
                    </div>\
                </div>\
            </div>';

            return contentString;
        }
        //
        if (document.getElementById('top-gmap-lock-btn')) {
            google.maps.event.addDomListener(document.getElementById('top-gmap-lock-btn'), 'click', function () {
                if (jQuery('#top-gmap-lock-btn').hasClass('map-loked')) {
                    map.setOptions({scrollwheel: true});
                    map.setOptions({draggable: true});
                    document.getElementById('top-gmap-lock-btn').innerHTML = '<i class="icon-unlock"></i>';
                    jQuery('#top-gmap-lock-btn').attr('class', 'top-gmap-lock-btn map-unloked');
                } else if (jQuery('#top-gmap-lock-btn').hasClass('map-unloked')) {
                    map.setOptions({scrollwheel: false});
                    map.setOptions({draggable: false});
                    document.getElementById('top-gmap-lock-btn').innerHTML = '<i class="icon-lock2"></i>';
                    jQuery('#top-gmap-lock-btn').attr('class', 'top-gmap-lock-btn map-loked');
                }
            });
        }
    }
}

jQuery(".foodbakery-ontop-gmap").css("pointer-events", "none");

var onTopMapMouseleaveHandler = function (event) {
    var that = jQuery(this);

    that.on('click', onTopMapClickHandler);
    that.off('mouseleave', onTopMapMouseleaveHandler);
    jQuery(".foodbakery-ontop-gmap").css("pointer-events", "none");
}

var onTopMapClickHandler = function (event) {
    var that = jQuery(this);
    // Disable the click handler until the user leaves the map area
    that.off('click', onTopMapClickHandler);

    // Enable scrolling zoom
    that.find('.foodbakery-ontop-gmap').css("pointer-events", "auto");

    // Handle the mouse leave event
    that.on('mouseleave', onTopMapMouseleaveHandler);
}
jQuery(document).on('click', '.foodbakery-top-map-holder', onTopMapClickHandler);