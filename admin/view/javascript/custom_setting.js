
/*!
 * Version: 1
 */
var custom_setting = {
    init: function (html) {
        if (!html) {
            var html = jQuery(document);
        }

        html.find('.codemirror').each(function () {
            custom_setting.codemirror.init(this);
        });

        html.find('.map').each(function () {
            custom_setting.map.init(this);
        });

        html.find('.input-autocomplete').each(function () {
            custom_setting.autocomplete.init(jQuery(this));
        });

        html.find('.input-multiple-autocomplete').each(function () {
            custom_setting.multiple_autocomplete.init(jQuery(this));
        });

        html.find('.custom-setting-range').each(function () {
            custom_setting.range.init(jQuery(this));
        });

        custom_setting.iconpicker.init(html.find('.iconpicker-element'));
        custom_setting.texteditor.init(html.find('.texteditor'));
        custom_setting.datetimepicker.init(html.find('.datetimepicker'));
        custom_setting.colorpicker.init(html.find('.colorpicker-component'));

        $.each(html.find('[data-mask]'), function () {
            jQuery(this).inputmask(jQuery(this).attr('data-mask'));
        });


        $.each(html.find('[type="radio"]'), function () {

            if (jQuery(this).closest('td').length) {
                if (jQuery(this).closest('td').find('[type="radio"]:checked').length == 0) {
                    jQuery(this).closest('td').find('[type="radio"]:first').click();
                }
            } else if (jQuery(this).closest('.form-group').length) {
                if (jQuery(this).closest('.form-group').find('[type="radio"]:checked').length == 0) {
                    jQuery(this).closest('.form-group').find('[type="radio"]:first').click();
                }
            }
        });

        html.find("ul.nav.nav-pills, ul.nav.nav-tabs").each(function () {
            jQuery(this).find("a[data-toggle=tab]:first, a[data-toggle=pill]:first").tab("show");
        });
        html.find('.tab-autocomplete').each(function () {


            var el = jQuery(this);
            var data_id = el.closest('[data-id]').attr('data-id');

            el.autocomplete({
                delay: 100,
                'minLength': 2,
                'source': function (request, response) {
                    if ($.trim(request) != el.data('search')) {
                        el.data('search', $.trim(request));
                        timer = setTimeout(function () {
                            $.ajax({
                                url: 'index.php?route=custom/setting/input_autocomplete&user_token=' + oc_settings['user_token'],
                                type: 'post',
                                data: {
                                    setting_id: el.attr('data-setting_id'),
                                    filter_name: $.trim(request),
                                },
                                dataType: 'json',
                                success: function (json) {

                                    el.data('json', json);
                                    response($.map(json, function (item, i) {
                                        return {
                                            label: item['title'],
                                            value: item['value'],
                                        }
                                    }));
                                }
                            });
                        }, 200);
                    } else {
                        if (el.data('json')) {
                            response($.map(el.data('json'), function (item, i) {
                                return {
                                    label: item['title'],
                                    value: item['value'],
                                    category: (typeof (item['group'] !== 'undefined') ? item['group'] : '')
                                }
                            }));
                        }
                    }
                },
                'select': function (item) {
                    addTab(el.attr('data-setting_id'), data_id, item['value']);
                    el.val('');

                }
            });
            el.on('keyup', function (e) {
                if (typeof timer) {
                    clearInterval(timer);
                }
            });;
        });

        html.find('.title-tab[data-popover="true"]').each(function () {
            custom_setting.popover.tab_name(jQuery(this));
        });
    },
    map: {
        init: function (el) {

            custom_setting.map[jQuery(el).attr('data-type')].init(el);

        },
        hasCoords: function (coords, set, type) {
            if (set) {
                var default_coords = oc_settings.coords.split(', ').map(Number);
                var latitude = default_coords[0];
                var longitude = default_coords[1];
            }
            if (coords) {
                var regex = /^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/g;
                if (regex.test(coords)) {
                    var coords = coords.split(', ').map(Number);
                    var latitude = coords[0];
                    var longitude = coords[1];
                }

            }

            if (typeof (latitude) != 'undefined' && typeof (longitude) != 'undefined') {
                switch (type) {
                    case 'yandex':
                    case '2gis':
                        return [latitude, longitude];
                        break;
                    case 'google':
                        return { lat: latitude, lng: longitude };
                        break;
                }

            }
        },
        '2gis': {
            init: function (el) {
                var type = jQuery(el).attr('data-type');
                var input = jQuery(el).attr('data-input');
                var $geocode = jQuery(input + '-geocode');
                var $center = jQuery(input + '-center');
                var $zoom = jQuery(input + '-zoom');

                var geocode = $geocode.val();
                var center = custom_setting.map.hasCoords($center.val(), true, type);
                var zoom = ($zoom.val() && Number($zoom.val()) >= 0 && Number($zoom.val()) <= 20 ? Number($zoom.val()) : 14);
                var coords = custom_setting.map.hasCoords(geocode, true, type);

                var latlng = coords;
                $center.val(Object.values(center)[0] + ', ' + Object.values(center)[1]);
                $geocode.val(Object.values(latlng)[0] + ', ' + Object.values(latlng)[1]);
                $zoom.val(zoom);

                DG.then(function () {

                    var marker;

                    var map = new DG.map(jQuery(el).attr('id'), {
                        center: center,
                        zoom: zoom
                    });

                    marker = DG.marker(coords, {
                        draggable: true,

                    }).addTo(map);
                    marker.on('dragend', function (e) {
                        var lat = e.target._latlng.lat,
                            lng = e.target._latlng.lng;
                        $geocode.val(lat + ', ' + lng);
                    });

                    map.on('moveend', function (e) {

                        var coords = map.getCenter();
                        var zoom = map.getZoom();
                        $zoom.val(zoom);
                        var lat = coords.lat;
                        var lng = coords.lng;
                        $center.val(lat + ', ' + lng);
                    });

                    map.on('click', function (e) {
                        console.log(e);
                        var coords = e.latlng;
                        var lat = coords.lat;
                        var lng = coords.lng;
                        var coords = [lat, lng];

                        marker.setLatLng(coords);

                        getAddress(coords);
                    });

                    function getAddress(coords) {
                        savecoordinats(coords);


                    }
                    function setCoordinates(coords) {
                        if (coords) {
                            marker.setLatLng(coords);
                            map.setView(coords);
                        }

                    }

                    $zoom.on('change', function () {
                        map.setZoom(Number(jQuery(this).val()));
                    });
                    $center.on('change', function () {
                        var coords = custom_setting.map.hasCoords(jQuery(this).val(), false, type);
                        setCoordinates(coords);

                    });
                    $geocode.on('change', function () {
                        var coords = custom_setting.map.hasCoords(jQuery(this).val(), false, type);
                        setCoordinates(coords);

                    });
                });
                function savecoordinats(coords) {
                    var new_coords = [
                        coords[0],
                        coords[1]
                    ].join(', ');
                    $geocode.val(new_coords);
                }
            }
        },
        google: {
            init: function (el) {
                var type = jQuery(el).attr('data-type');
                var input = jQuery(el).attr('data-input');
                var $geocode = jQuery(input + '-geocode');
                var $center = jQuery(input + '-center');
                var $zoom = jQuery(input + '-zoom');
                var $search = jQuery(input + '-search');
                var map = jQuery(el).attr('id');

                var geocode = $geocode.val();
                var center = custom_setting.map.hasCoords($center.val(), true, type);
                var zoom = ($zoom.val() && Number($zoom.val()) >= 0 && Number($zoom.val()) <= 20 ? Number($zoom.val()) : 14);
                var coords = custom_setting.map.hasCoords(geocode, true, type);



                var latlng = coords;
                $center.val(Object.values(center)[0] + ', ' + Object.values(center)[1]);
                $geocode.val(Object.values(latlng)[0] + ', ' + Object.values(latlng)[1]);
                $zoom.val(zoom);



                var map = new google.maps.Map(document.getElementById(map), {
                    center: center,
                    zoom: zoom, //The zoom value for map
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    streetViewControl: false,
                    mapTypeControl: false,
                });
                var geocoder = new google.maps.Geocoder();
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    draggable: true //this makes it drag and drop
                });

                geocodePosition(latlng);

                var input = $search.get(0);
                var searchBox = new google.maps.places.SearchBox(input);
                map.addListener("bounds_changed", function () {
                    searchBox.setBounds(map.getBounds());
                });
                searchBox.addListener("places_changed", function () {
                    const places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }
                    // Clear out the old markers.

                    // For each place, get the icon, name and location.
                    const bounds = new google.maps.LatLngBounds();
                    places.forEach(function (place) {
                        if (!place.geometry) {
                            console.log("Returned place contains no geometry");
                            return;
                        }
                        const icon = {
                            url: place.icon,
                            size: new google.maps.Size(71, 71),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(25, 25),
                        };
                        // Create a marker for each place.
                        var lat = place.geometry.location.lat();
                        var lng = place.geometry.location.lng();
                        $geocode.val(lat + ', ' + lng);
                        setCoordinates({ lat, lng });


                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    })
                });

                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                google.maps.event.addListener(map, 'click', function (l) {
                    lat = l.latLng.lat();
                    lng = l.latLng.lng();
                    marker.setPosition({ lat, lng });
                    geocodePosition({ lat, lng });
                    $geocode.val(lat + ', ' + lng);

                });

                var infoWindow = new google.maps.InfoWindow({
                    content: 'This is an info window'
                });

                google.maps.event.addListener(marker, 'click', function () {
                    infoWindow.open(map, marker);
                });

                function updateMarkerAddress(str) {
                    infoWindow.setContent(str);;
                }

                function geocodePosition(pos) {
                    geocoder.geocode({
                        latLng: pos
                    }, function (responses) {
                        if (responses && responses.length > 0) {
                            updateMarkerAddress(responses[0].formatted_address);
                        } else {
                            updateMarkerAddress('Cannot determine address at this location.');
                        }
                    });
                }

                google.maps.event.addListener(map, 'dragend', function (a) {
                    var a = map.getCenter();
                    $center.val(a.lat() + ', ' + a.lng());
                });

                google.maps.event.addListener(map, 'zoom_changed', function (a) {
                    var a = map.getCenter();
                    $center.val(a.lat() + ', ' + a.lng());
                    $zoom.val(map.getZoom());
                });

                google.maps.event.addListener(marker, 'dragend', function (a) {
                    var lat = a.latLng.lat();
                    var lng = a.latLng.lng();
                    $geocode.val(lat + ', ' + lng);
                    geocodePosition({ lat, lng });

                });

                function setCoordinates(coords) {
                    map.panTo(coords);
                    marker.setPosition(coords);
                    geocodePosition(coords);
                    var centrer = Object.values(coords);
                    $center.val(centrer[0] + ', ' + centrer[1]);

                }

                $zoom.on('change', function () {
                    map.setZoom(Number(jQuery(this).val()));
                });

                $center.on('change', function () {
                    var coords = custom_setting.map.hasCoords(jQuery(this).val(), false, type);
                    setCoordinates(coords);
                });

                $geocode.on('change', function () {
                    var coords = custom_setting.map.hasCoords(jQuery(this).val(), false, type);
                    setCoordinates(coords);
                });
            }
        },
        yandex: {
            init: function (el) {
                var type = jQuery(el).attr('data-type');
                var input = jQuery(el).attr('data-input');
                var $geocode = jQuery(input + '-geocode');
                var $center = jQuery(input + '-center');
                var $zoom = jQuery(input + '-zoom');
                var map = jQuery(el).attr('id');

                var geocode = $geocode.val();
                var center = custom_setting.map.hasCoords($center.val(), true, type);
                var zoom = ($zoom.val() && Number($zoom.val()) >= 0 && Number($zoom.val()) <= 20 ? $zoom.val() : 14);
                var coords = custom_setting.map.hasCoords(geocode, true, type);

                ymaps.ready(function () {

                    var yandexPlacemark,
                        yandexMap = new ymaps.Map(map, {
                            center: center,
                            zoom: zoom,
                            controls: []
                        });

                    var searchControl = new ymaps.control.SearchControl({
                        options: {
                            provider: 'yandex#search'
                        }
                    });

                    yandexMap.controls.add(searchControl);
                    var zoomControl = new ymaps.control.ZoomControl({
                        options: {
                            size: "small",
                            position: {
                                right: 15,
                                top: 'auto',
                                bottom: 50,
                            }
                        }
                    });

                    yandexMap.controls.add(zoomControl);
                    yandexMap.controls.add('fullscreenControl');

                    var yandexPlacemark = new ymaps.Placemark(coords, {
                        iconCaption: 'поиск...'
                    }, {
                        preset: 'islands#blueDotIconWithCaption',
                        draggable: true
                    });

                    yandexMap.geoObjects.add(yandexPlacemark);
                    getAddress(coords);

                    yandexPlacemark.events.add('dragend', function () {
                        getAddress(yandexPlacemark.geometry.getCoordinates());
                    });

                    yandexMap.events.add('click', function (e) {
                        var coords = e.get('coords');
                        if (yandexPlacemark) {
                            yandexPlacemark.geometry.setCoordinates(coords);
                        } else {
                            yandexPlacemark = createPlacemark(coords);
                            yandexMap.geoObjects.add(yandexPlacemark);
                            yandexPlacemark.events.add('dragend', function () {
                                getAddress(yandexPlacemark.geometry.getCoordinates());
                            });
                        }
                        getAddress(coords);
                    });

                    function createPlacemark(coords) {
                        return new ymaps.Placemark(coords, {
                            iconCaption: 'поиск...'
                        }, {
                            hasBalloon: false,
                            preset: 'islands#blueDotIconWithCaption',
                            draggable: true
                        });
                    }

                    searchControl.events.add('resultselect', function (e) {
                        var index = e.get('index');
                        searchControl.getResult(index).then(function (res) {
                            var coords = res.geometry.getCoordinates();
                            yandexPlacemark.geometry.setCoordinates(coords);
                            getAddress(coords);
                        });
                    });

                    yandexMap.events.add('boundschange', function (event) {
                        console.log(yandexMap.getCenter());
                        $center.val(yandexMap.getCenter().filter(Boolean).join(', '));
                        $zoom.val(yandexMap.getZoom());
                    });

                    function getAddress(coords) {
                        savecoordinats(coords);
                        ymaps.geocode(coords).then(function (res) {
                            var firstGeoObject = res.geoObjects.get(0);
                            yandexPlacemark.properties.set({
                                iconCaption: [
                                    firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                                    firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                                ].filter(Boolean).join(', '),
                                balloonContent: firstGeoObject.getAddressLine()
                            });
                        });
                    }

                    function savecoordinats(coords) {
                        var new_coords = [
                            coords[0],
                            coords[1]
                        ].join(', ');
                        $center.val(yandexMap.getCenter().filter(Boolean).join(', '));
                        $zoom.val(yandexMap.getZoom());
                        $geocode.val(new_coords);
                    }
                    function setCoordinates(coords) {
                        yandexMap.panTo(
                            // Координаты нового центра карты
                            coords, {
                            /* Опции перемещения:
                               разрешить уменьшать и затем увеличивать зум
                               карты при перемещении между точками 
                            */
                            flying: true
                        }
                        )
                        if (yandexPlacemark) {
                            yandexPlacemark.geometry.setCoordinates(coords);
                        } else {
                            yandexPlacemark = createPlacemark(coords);
                            yandexMap.geoObjects.add(yandexPlacemark);
                            yandexPlacemark.events.add('dragend', function () {
                                getAddress(yandexPlacemark.geometry.getCoordinates());
                            });
                        }

                    }
                    $zoom.on('change', function () {

                        yandexMap.setZoom(jQuery(this).val());

                    });
                    $center.on('change', function () {
                        var coords = custom_setting.map.hasCoords(jQuery(this).val(), false, type);
                        setCoordinates(coords);

                    });
                    $geocode.on('change', function () {
                        var coords = custom_setting.map.hasCoords(jQuery(this).val(), false, type);
                        setCoordinates(coords);

                    });
                });

            },
            ready: function () {

            }

        }
    },
    popover: {
        tab_name: function (el) {
            var $setting = el.closest('[data-name]');
            var name = $setting.attr('data-name');
            var id = $setting.attr('id');
            var tab_id = el.attr('data-tab_id');

            el.popover({
                container: 'body',

                content: function () {

                    var html = '';
                    $.each(oc_settings['languages'], function (index, language) {

                        html += '  <div class="input-group" style="margin-bottom: 5px;">';
                        html += '      <span class="input-group-addon input-sm"><img src="language/' + language.code + '/' + language.code + '.png" title="' + language.name + '" /></span>';
                        html += '      <input value="' + jQuery('[name="custom_setting' + name + '[' + tab_id + '][descriptions][' + language.language_id + '][title]"]').val() + '" placeholder="' + oc_settings['text']['entry_tab_name'] + '" type="text" data-name="' + name + '"  data-id="#' + id + '-row-' + tab_id + '" data-tab_id="' + tab_id + '" data-language="' + language.language_id + '" class="form-control input-sm tab-name-change"/>';
                        html += '  </div>';
                    });

                    return html;
                },
                title: oc_settings['text']['entry_tab_name'] +
                    '<button type="button" class="close" onclick="jQuery(this).closest(\'.popover\').popover(\'hide\');">&times;</button>',
                html: true,
                trigger: 'click',
                placement: function (context, source) {
                    var position = jQuery(source).position();

                    if (jQuery(source).offset().left > 515) {

                        return ($setting.attr('data-vertical') == '1' ? 'left' : 'top');

                    }
                    if (position.left < 515) {
                        return "top";
                    }
                    if (position.top < 110) {
                        return "bottom";
                    }
                    return "top";
                }

            });
        },
    },
    codemirror: {
        init: function (el, code, init) {
            if (jQuery(el).length) {
                var code = (jQuery(el).attr('data-code') ? jQuery(el).attr('data-code') : '');
                jQuery.ajax({
                    type: "GET",
                    url: 'view/javascript/custom_setting/codemirror/' + code + '/' + code + '.js',
                    dataType: "script",
                    complete: function (XMLHttpRequest, textStatus, errorThrown) {
                        if (code == 'htmlmixed') {
                            code = {
                                name: "htmlmixed",
                                scriptTypes: [{
                                    matches: /\/x-handlebars-template|\/x-mustache/i,
                                    mode: null
                                },
                                {
                                    matches: /(text|application)\/(x-)?vb(a|script)/i,
                                    mode: "vbscript"
                                }]
                            };
                        }
                        if (code == 'twig') {
                            code = { name: "twig", base: "text/html" };
                        }
                        if (code == 'php') {
                            code = 'text/x-php';
                        }
                        if (code == 'xml') {
                            code = 'application/xml';
                        }
                        var codemirror = CodeMirror.fromTextArea(el, {

                            height: '250px',
                            lineNumbers: true,
                            theme: 'monokai',
                            mode: code,
                            autoRefresh: true,
                            value: jQuery(el).val()

                        })
                        codemirror.on('change', function (cm) {
                            jQuery(el).val(cm.getValue());

                        });
                        jQuery(el).parents('.tab-pane').each(function () {
                            var tab = jQuery('a[href="#' + jQuery(this).attr('id') + '"]');
                            tab.on('shown.bs.tab', function () {
                                codemirror.refresh();
                            })
                        });


                        codemirror.setValue(jQuery(el).val());
                        codemirror.refresh();
                    },

                });
            }
        }
    },
    range: {
        init: function (el) {
            if (jQuery(el).length) {
                var $range = jQuery(el).children('.input-range');
                var $from = jQuery(el).children('.input-range-from');
                var $to = jQuery(el).children('.input-range-to');
                var min = $range.attr('data-min');
                var max = $range.attr('data-max');
                var step = Number($range.attr('data-step'));
                var type = Number($range.attr('data-type'));
                var grid = Number($range.attr('data-grid'));
                $range.ionRangeSlider({
                    type: type,
                    step: step,
                    min: min,
                    max: max,
                    from: ($from.val() == '' ? min : $from.val()),
                    to: ($to.val() == '' ? max : $to.val()),
                    drag_interval: false,
                    prettify_enabled: false,
                    grid: (grid == 'true'),
                    onFinish: function (data) {
                        $form.val(data.from);
                        $to.val(data.to);
                    }
                });
            }
        },
    },
    iconpicker: {
        init: function (el) {
            if (jQuery(el).length) {
                jQuery(el).iconpicker({
                    fullClassFormatter: function (val) {
                        return 'fa ' + val;
                    },
                    animation: false,
                    hideOnSelect: true,
                    collision: true,
                    mustAccept: true,
                    templates: {
                        popover: '<div class="iconpicker-popover popover"><div class="arrow"></div>' + '<div class="popover-title"></div><div class="popover-content"></div></div>',
                        footer: '<div class="popover-footer"></div>',
                        buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' + ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
                        search: '<input type="search" class="form-control iconpicker-search" placeholder="Type to filter" />',
                        iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
                        iconpickerItem: '<a role="button" href="javascript::" class="iconpicker-item"><i></i></a>'
                    },

                });
            }
        },
    },
    datetimepicker: {
        init: function (el) {
            if (el.length) {
                el.datetimepicker({
                    format: (el.attr('data-format') ? el.attr('data-format') : ''),
                    pickTime: (!el.attr('data-pick') || el.attr('data-pick') == 'all' || el.attr('data-pick') == 'time' ? true : false),
                    pickDate: (!el.attr('data-pick') || el.attr('data-pick') == 'all' || el.attr('data-pick') == 'date' ? true : false),
                    language: oc_settings['datepicker'],

                });
            }
        }

    },
    colorpicker: {
        init: function (el) {
            if (el.length) {
                el.colorpicker({
                    colorSelectors: {
                        'black': '#000000',
                        'white': '#ffffff',
                        'red': '#FF0000',
                        'default': '#777777',
                        'primary': '#337ab7',
                        'success': '#5cb85c',
                        'info': '#5bc0de',
                        'warning': '#f0ad4e',
                        'danger': '#d9534f'
                    },
                    customClass: 'cs-colorpicker',
                    format: (el.attr('data-format') == 'auto' ? false : el.attr('data-format')),
                    align: 'left',
                    component: '.add-on, .input-group-addon',
                    sliders: {
                        saturation: {
                            maxLeft: 125,
                            maxTop: 125
                        },
                        hue: {
                            maxTop: 125
                        },
                        alpha: {
                            maxTop: 125
                        }
                    }
                });
            }
        }
    },
    texteditor: {
        init: function (el) {
            if (el.length) {
                el.summernote({
                    height: 250,
                });
            }
        }
    },
    multiple_autocomplete: {
        init: function (el) {
            var timer;
            if (el.length) {
                el.autocomplete({
                    delay: 100,
                    'minLength': 2,
                    'source': function (request, response) {
                        if ($.trim(request) != el.data('search')) {
                            el.data('search', $.trim(request));
                            timer = setTimeout(function () {
                                $.ajax({
                                    url: 'index.php?route=custom/setting/input_autocomplete&user_token=' + oc_settings['user_token'],
                                    type: 'post',
                                    data: {
                                        setting_id: el.attr('data-id'),
                                        filter_name: $.trim(request),
                                    },
                                    dataType: 'json',
                                    success: function (json) {

                                        el.data('json', json);
                                        response($.map(json, function (item, i) {
                                            return {
                                                label: item['title'],
                                                value: item['value'],
                                                index: i,
                                                category: (typeof (item['group'] !== 'undefined') ? item['group'] : '')
                                            }
                                        }));
                                    }
                                });
                            }, 200);
                        } else {
                            if (el.data('json')) {
                                response($.map(el.data('json'), function (item, i) {
                                    return {
                                        label: item['title'],
                                        index: i,
                                        value: item['value'],
                                        category: (typeof (item['group'] !== 'undefined') ? item['group'] : '')
                                    }
                                }));
                            }
                        }
                    },
                    'select': function (item) {
                        el.val('');
                        jQuery('[name="' + el.attr('name') + '[' + item['value'] + '][value]"]').parent().remove();
                        jQuery('#' + el.attr('id') + '-multiple').append('<div id="' + el.attr('id') + '-multiple-' + item['index'] + '-value" ><i class="fa fa-minus-circle" onclick="jQuery(this).parent().remove();"></i> ' + item['label'] + '<input type="hidden" name="' + el.attr('name') + '[' + item['value'] + '][value]' + '" value="' + item['value'] + '" /><input type="hidden" name="' + el.attr('name') + '[' + item['value'] + '][title]' + '" value="' + item['label'] + '" /></div>');
                        this.hide();
                    }
                });
                el.on('keydown', function (e) {
                    if (typeof timer) {
                        clearInterval(timer);
                    }
                });
            }
        }
    },
    autocomplete: {
        init: function (el) {
            var timer;
            if (el.length) {
                el.autocomplete({
                    delay: 100,
                    'minLength': 2,
                    'source': function (request, response) {
                        if ($.trim(request) != el.data('search')) {
                            el.data('search', $.trim(request));
                            timer = setTimeout(function () {
                                $.ajax({
                                    url: 'index.php?route=custom/setting/input_autocomplete&user_token=' + oc_settings['user_token'],
                                    type: 'post',
                                    data: {
                                        setting_id: el.attr('data-id'),
                                        filter_name: $.trim(request),
                                    },
                                    dataType: 'json',
                                    success: function (json) {
                                        if (parseInt(el.attr('data-empty'))) {
                                            json.unshift({
                                                title: oc_settings.text['text_none'],
                                                value: ' ',
                                            });
                                        }
                                        el.data('json', json);
                                        response($.map(json, function (item, i) {
                                            return {
                                                label: item['title'],
                                                value: item['value'],
                                                category: (typeof (item['group'] !== 'undefined') ? item['group'] : '')
                                            }
                                        }));
                                    }
                                });
                            }, 200);
                        } else {
                            if (el.data('json')) {
                                response($.map(el.data('json'), function (item, i) {
                                    return {
                                        label: item['title'],
                                        value: item['value'],
                                        category: (typeof (item['group'] !== 'undefined') ? item['group'] : '')
                                    }
                                }));
                            }
                        }
                    },
                    'select': function (item) {
                        el.val(item['label']);
                        jQuery('#' + el.attr('id') + '-value').val(item['value']);
                        jQuery('#' + el.attr('id') + '-title').val(item['label']);
                        this.hide();
                    }
                });
                el.on('keyup', function (e) {
                    if (typeof timer) {
                        clearInterval(timer);
                    }
                });
            }
        }
    },

}

jQuery(document).ready(function () {

    if (typeof (oc_settings) !== 'undefined') {
        custom_setting.init();

    }

    jQuery(window).load(function () {
        jQuery('.custom-setting-view-code-collapse').each(function () {
            jQuery(this).one('shown.bs.collapse', function () {
                if (jQuery(this).find('.custom-setting-code').length) {
                    jQuery(this).find('.custom-setting-code').each(function () {
                        var $this = jQuery(this);
                        var code = $this.attr('data-code');
                        if (code == 'twig') {
                            var code = { name: "twig", base: "text/html" };
                        }
                        if (code == 'php') {
                            var code = 'text/x-php';
                        }
                        if (code == 'xml') {
                            var code = 'application/xml';
                        }
                        var codemirror = CodeMirror.fromTextArea(this, {
                            mode: code,
                            indentWithTabs: 4,
                            lineNumbers: true,
                            theme: 'monokai',
                            readOnly: true
                        });
                        codemirror.refresh();
                    });

                }
            });
        });
    });
    jQuery('body').on('click', function (e) {

        jQuery('[data-popover="true"]').each(function () {
            // hide any open popovers when the anywhere else in the body is clicked
            if (!jQuery(this).is(e.target) && jQuery(this).has(e.target).length === 0 && jQuery(e.target).closest('.popover').length === 0 && jQuery('.popover').has(e.target).length === 0) {
                var $popover = jQuery(this).data('bs.popover');
                if ($popover) {
                    $popover.hide();
                }
                jQuery(this).popover('hide');
            }
        });
    });
    jQuery(document).on('keyup', 'input.tab-name-change[data-language]', function () {
        var tab_id = jQuery(this).attr('data-tab_id');
        var name = jQuery(this).attr('data-name');
        var id = jQuery(this).attr('data-id');
        var value = $.trim(jQuery(this).val());
        var language_id = jQuery(this).attr('data-language');

        if (!value) {
            var value = oc_settings.text['text_tab'] + ' ' + (Number(tab_id) + 1);
        }

        if (language_id == oc_settings['language_id']) {
            jQuery('[href="' + id + '"]').find('.tab-name').text(value);
        }

        jQuery('[name="custom_setting' + name + '[' + tab_id + '][descriptions][' + language_id + '][title]"]').val(value);
    });
});
function Igenerate(el, search_el, attr, name, i) {
    if (!i) {
        var i = jQuery(el).find(search_el).length;
    }

    if (jQuery(el).find(search_el + '[' + attr + '="' + name + i + '"]').length) {
        i++;
        return Igenerate(el, search_el, attr, name, i)
    } else {
        return i;
    }
};
function removeTab(button, id, row) {
    if (button) {
        jQuery(button).tooltip('destroy');
    }

    jQuery('#' + id + '-row-' + row).remove();
    jQuery('a[href="#' + id + '-row-' + row + '"]').parent().remove();


    if (jQuery('#nav-' + id).children('.active').length == 0) {
        jQuery('#nav-' + id).children().find('a').first().tab('show')
    }
    jQuery('.popover').popover('hide');
}

function addTab(setting_id, id, row=0) {
    var $tabs = jQuery('#setting-value-block-' + setting_id + '-' + id);
    var data_id = $tabs.attr('data-id');

    var data_name = $tabs.attr('data-name');

    if (row) {
        var tab_id = row;
    } else {
        var tab_id = Igenerate('#setting-value-block-' + setting_id + '-' + id, '.tab-content:first > .tab-pane', 'id', 'setting-value-block-' + setting_id + '-' + id + '-row-');
    }

    if (jQuery('#nav-setting-value-block-' + setting_id + '-' + id).find('[data-tab_id="' + tab_id + '"]').length) {

        jQuery('#nav-setting-value-block-' + setting_id + '-' + id).find('[data-tab_id="' + tab_id + '"]').find('a').tab('show');
        return;
    }
    $tabs.data('value_row', tab_id)

    $.ajax({
        url: $.trim('index.php?route=custom/setting/getTabSetting&user_token=' + oc_settings['user_token']),
        type: 'post',
        dataType: 'json',
        data: {
            setting_id: setting_id,
            data_id: data_id,
            data_name: data_name,
            index: tab_id
        },
        beforeSend: function () {
            $tabs.find('.button-add-tab, .tab-autocomplete').button('loading');

        },
        complete: function () {
            jQuery('[data-toggle="tooltip"]').tooltip('hide');
            $tabs.find('.button-add-tab, .tab-autocomplete').button('reset');

        },
        success: function (json) {

            if (json['data']) {
                var tab_name = json.tab_name;
                var html = '';
                var nav = '<li class="title-tab" data-tab_id="' + tab_id + '" data-popover="true"><a href="#setting-value-block-' + setting_id + '-' + id + '-row-' + tab_id + '" data-toggle="tab" aria-expanded="false"><span class="tab-name">' + tab_name + '</span> <i class="fa fa-minus-circle" onclick="confirm(oc_settings.text.text_confirm) ?  removeTab(this, \'setting-value-block-' + setting_id + '-' + id + '\', ' + tab_id + ') : false" data-toggle="tooltip" title="' + oc_settings.text.button_remove + '"></i></a></li>';
                html += '<div class="tab-pane" id="setting-value-block-' + setting_id + '-' + id + '-row-' + tab_id + '">';

                $.each(oc_settings['languages'], function (index, language) {
                    html += '<input type="hidden" value="' + tab_name + '" name="custom_setting' + json['setting_info']['name'] + '[' + tab_id + '][descriptions][' + language.language_id + '][title]"/>';
                });
                $.each(json['data'], function (key_id, data) {
                    html += '' + data['setting']['output'] + '';
                });
                html += '</div>';

                var $html = jQuery(html);
                var $nav = jQuery(nav);



                jQuery('#tabs-' + $tabs.attr('id')).append($html).promise().done(function () {


                    if (jQuery('#tabs-' + $tabs.attr('id')).find("a[data-toggle=tab].active, a[data-toggle=pill].active").length == 0) {
                        jQuery('#tabs-' + $tabs.attr('id')).find("a[data-toggle=tab]:first").tab("show");
                        jQuery('#tabs-' + $tabs.attr('id')).find("a[data-toggle=tab]:first").focus();
                    }

                });


                jQuery('#nav-' + $tabs.attr('id')).children('li:last-child').before($nav).promise().done(function () {
                    $nav.find('a').tab('show');
                    if (!row) {
                        custom_setting.popover.tab_name($nav);

                        $nav.popover('show');
                    }
                });

                custom_setting.init($html);



            }

        },
    });
}
function addSettingValueRow(setting_id, id) {
    var table = jQuery('#setting-value-block-' + setting_id + '-' + id);
    var data_id = table.attr('data-id');
    var data_name = table.attr('data-name');

    table.data('value_row', Igenerate('#setting-value-block-' + setting_id + '-' + id, 'tbody tr', 'id', 'setting-value-block-' + setting_id + '-' + id + '-row-'));

    $.ajax({
        url: $.trim('index.php?route=custom/setting/getBlockSetting&user_token=' + oc_settings['user_token']),
        type: 'post',
        dataType: 'json',
        data: {
            setting_id: setting_id,
            data_id: data_id,
            data_name: data_name,
            index: table.data('value_row')
        },
        beforeSend: function () {
            table.find('button').prop('disabled', true);
        },
        complete: function () {
            jQuery('[data-toggle="tooltip"]').tooltip('hide');
            table.find('button').prop('disabled', false);
        },
        success: function (json) {
            if (json['data']) {

                var html = '';
                html += '<tr id="setting-value-block-' + setting_id + '-' + id + '-row-' + table.data('value_row') + '">';
                $.each(json['data'], function (key_id, data) {
                    html += '<td class="text-left">' + data['setting']['output'] + '</td>';
                });

                if (table.find('tfoot').length) {
                    html += '<td class="text-right"><button type="button" onclick="confirm(oc_settings.text.text_confirm) ? (jQuery(this).tooltip(\'destroy\'), jQuery(\'#setting-value-block-' + setting_id + '-' + id + '-row-' + table.data('value_row') + '\').remove()) : false;" data-toggle="tooltip" class="btn btn-danger"  title="' + oc_settings.text.button_remove + '"><i class="fa fa-minus-circle"></i></button></td>';
                }
                html += '</tr>';

                var $html = jQuery(html);

                table.children('tbody').append($html);
                custom_setting.init($html);


            }

        },
    });


}
