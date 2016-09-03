/**
 * Created by truongsa on 8/13/16.
 */

/*!
 * jQuery serializeObject - v0.2 - 1/20/2010
 * http://benalman.com/projects/jquery-misc-plugins/
 *
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */

// Whereas .serializeArray() serializes a form into an array, .serializeObject()
// serializes a form into an (arguably more useful) object.

(function($,undefined){
    '$:nomunge'; // Used by YUI compressor.

    $.fn.serializeObject = function(){
        var obj = {};

        $.each( this.serializeArray(), function(i,o){
            var n = o.name,
                v = o.value;

            obj[n] = obj[n] === undefined ? v
                : $.isArray( obj[n] ) ? obj[n].concat( v )
                : [ obj[n], v ];
        });

        return obj;
    };

})(jQuery);

( function( $, window ) {
    jQuery.fn.scrollToEl = function ( elem ) {
        $(this).animate({
            scrollTop:  $(this).scrollTop() - $(this).offset().top + elem.offset().top
        }, 200 );
        return this;
    };
}( jQuery, window ));

var mapify = {
    current_action: null,
};

( function( $, window ) {

    var maps = {};

    function getTemplate( tmpl_id, data ){
        if ( typeof data === "undefined" ) {
            data = {};
        }
        /**
         * Function that loads the Mustache template
         */
        var t = _.memoize( function ( _tmpl_id, _data ) {
            var compiled,
            /*
             * Underscore's default ERB-style templates are incompatible with PHP
             * when asp_tags is enabled, so WordPress uses Mustache-inspired templating syntax.
             *
             * @see track ticket #22344.
             */
                options = {
                    evaluate: /<#([\s\S]+?)#>/g,
                    interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
                    escape: /\{\{([^\}]+?)\}\}(?!\})/g,
                    variable: 'data'
                };
            var html = jQuery( '#' +_tmpl_id ).html();
            //console.log( html );
            compiled = _.template( html, null, options);
            return compiled( _data );
        });

        return t( tmpl_id,  data );
    }

    function addItemMap( data, map_item ){
        var mapOptions, map;
        try {
            if ( 'string' === typeof data ) {
                data = JSON.parse(data);
            }
            data.center_latitude = parseFloat( data.center_latitude );
            data.center_longitude = parseFloat( data.center_longitude );
            data.zoom_level = parseFloat( data.zoom_level );
            mapOptions = {
                center: new google.maps.LatLng( data.center_latitude, data.center_longitude ),
                zoom: data.zoom_level,
                disableDoubleClickZoom: false,
                scrollwheel: false,
                panControl: false,
                zoomControl: false,
                streetViewControl: false,
                mapTypeControl: false,
            };
            map = new google.maps.Map( map_item.find('.gmap-preview')[0], mapOptions );
        } catch ( e ) {
            mapOptions = {
                center: new google.maps.LatLng( 54.800685, -4.130859 ),
                zoom: 12,
                disableDoubleClickZoom: false,
                scrollwheel: false,
                panControl: false,
                zoomControl: false,
                streetViewControl: false,
                mapTypeControl: false,
            };
            map = new google.maps.Map( map_item.find('.gmap-preview')[0], mapOptions );
        }
        maps[ data.map_id ] = map;
    }

    function appendMapToList( map_data ){
        var col = $( '<div class="map-col"></div>' );
        var tpl = getTemplate( 'mapify-loop-map-tpl', map_data );
        col.append( tpl );
        $( '.mapify-maps').append( col );
        addItemMap( map_data, col );
    }


    function MapifyAdminControler(){

        $( '.mapify-new').on( 'click', function( e ) {
            var new_map_data = {
                map_id: 0, // 0 mean add new
                map_title:  false
            };
            new_map_data = $.extend( {}, mapify_config.default_map_data, new_map_data );

            map_modal = $( getTemplate( 'mapify-map-template', new_map_data ) );
            $( 'body' ).append( map_modal );
            $( 'body' ).append( '<div class="media-modal-backdrop"></div>' );

            // Insert locations
            var locate_tpl =  $( getTemplate( 'mapify-locations-template' ) );
            $( '.attachments-browser', map_modal ).append( locate_tpl );
            mapifyAdmin( map_modal, locate_tpl, new_map_data );

        } );


        $( 'body').on( 'click', '.mapify-map-item', function( e ){
            e.preventDefault();
            var map_item = $( this );
            var map_id = map_item.attr( 'data-map-id' );
            var del_btn = map_item.find( '.del-map' );
            var action;

            if ( $(e.target).is( '.shortcode' ) ) {
                return;
            }

            if ( del_btn.is(e.target) || del_btn.has(e.target).length > 0 )
            {
                action = 'mapify_del_map';
            } else {
                action = 'mapify_load_map';
            }

            var _do = true;
            if ( 'mapify_del_map' === action ) {
                _do = confirm( mapify_config.confirm );
            }

            if ( _do ) {
                $.ajax({
                    url: mapify_config.ajax_url,
                    type: 'POST',
                    data: {
                        action: action,
                        _nonce: mapify_config.nonce,
                        map_id: map_id,
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (res) {

                        if (res.success) {
                            if ('mapify_load_map' === action) {
                                map_modal = $(getTemplate('mapify-map-template', res.data.map));
                                $('body').append(map_modal);
                                $('body').append('<div class="media-modal-backdrop"></div>');

                                // Insert locations
                                var locate_tpl = $(getTemplate('mapify-locations-template'));
                                $('.attachments-browser', map_modal).append(locate_tpl);
                                mapifyAdmin(map_modal, locate_tpl, res.data.map, res.data.locations);
                            } else {
                                map_item.closest('.map-col').remove();
                                delete maps[map_id];
                            }

                        }
                    }
                });
            }

        } );

        // Ajax load maps
        $.ajax({
            url: mapify_config.ajax_url,
            type: 'POST',
            data: {
                action: 'mapify_load_maps',
                _nonce: mapify_config.nonce,
            },
            cache: false,
            dataType: 'json',
            success: function( res ){
                if ( res.success ) {
                    $.each( res.data, function( map_id, data ){
                        appendMapToList( data );
                    } );

                }
            }
        });


    }

    function mapifyAdmin( map_modal, locate_tpl, map_data, map_locations ){
        if ( typeof map_locations === "undefined" ) {
            map_locations = {};
        }
        var delay = 400, locations = {}, gmap, data_changed = {
            map: {
                map_id: map_data.map_id
            },
            locations: {},
            map_id: map_data.map_id
        };

        /*
        map_modal = $( getTemplate( 'mapify-map-template' ) );
        $( 'body' ).append( map_modal );
        $( 'body' ).append( '<div class="media-modal-backdrop"></div>' );

        // Insert locations
        var locate_tpl =  $( getTemplate( 'mapify-locations-template' ) );
        $( '.attachments-browser', map_modal ).append( locate_tpl );
        */

        function enableSaveData(){
            $( '.mapify-save' ).removeAttr( 'disabled' );
        }

        function disableSaveData(){
            $( '.mapify-save' ).attr( 'disabled', 'disabled' );
        }

        function getInputVal( input ){
            var tagname = input.prop("tagName");
            var value = '';
            if ( tagname == 'INPUT' ) {
                if ( input.is(':checkbox') ) {
                    if ( input.is(":checked") ) {
                        value = input.val();
                    } else {
                        value = '';
                    }

                } else if ( input.is(':radio') )  {
                    if ( input.is(":checked") ) {
                        value = input.val();
                    } else {
                        value = '';
                    }
                } else {
                    value = input.val();
                }
            } else {
                value = input.val();
            }

            return value;
        }

        function toggleGroupSettings( li, action, open_cb, close_cb ){
            if ( typeof action === "undefined" || ! action ) {
                if ( li.hasClass( 'opened' ) ) {
                    action = 'close';
                } else {
                    action = 'open'
                }
            }

            if ( li.hasClass( 'locations' ) ) {
                if ( action == 'open' ) {
                    if ( li.hasClass( 'opened' ) ) {
                        return ;
                    }
                    li.addClass( 'opened' );
                    toggleLocationsSidebar( 'open' );
                    li.trigger( 'opened' );
                } else {
                    li.removeClass( 'opened' );
                    toggleLocationsSidebar( 'close' );
                    li.trigger( 'closed' );
                }
            } else {
                if ( action == 'open' ) {
                    if ( li.hasClass( 'opened' ) ) {
                        return ;
                    }
                    $( '.map-option-group', map_modal ).find( 'li' ).removeClass( 'opened' ).find( '.map-og-settings' ).slideUp( delay );
                    li.addClass( 'opened' ).find( '.map-og-settings' ).slideDown( delay,function(){
                        if ( typeof open_cb == "function" ) {
                            open_cb();
                        }
                        li.trigger( 'opened' );
                    } );

                } else {
                    li.removeClass( 'opened' ).find( '.map-og-settings' ).slideUp( delay, function(){
                        if ( typeof close_cb == "function" ) {
                            close_cb();
                        }
                        li.trigger( 'closed' );
                    } );
                }
            }
        }

        /**
         * Toggle Locations Sidebar
         * @param t open or close
         */
        function toggleLocationsSidebar( t, cb ){
            if ( t == 'open' ) {
                var right = $('.media-sidebar', map_modal).width();
                locate_tpl.animate({
                    left: right,
                }, {
                    queue: false,
                    duration: delay
                }, function () {

                    if ( typeof cb == "function" ) {
                        cb();
                    }
                });

                $('.attachments', map_modal).animate({
                    left: locate_tpl.width() + right,
                },{
                    queue: false,
                    duration: delay
                }, function () {

                });

            } else {
                closeLocationsSidebar();
            }
        }

        // Toggle sidebar group settings.
        map_modal.on( 'click', '.map-option-group li .map-og-heading', function( e ){
            e.preventDefault();
            toggleGroupSettings( $( this ).closest( 'li' ) );
        } );

        /**
         * Close locations sidebar
         */
        function closeLocationsSidebar(){
            $( 'li.group-locations', map_modal).removeClass( 'opened' );
            $( '.locations-sidebar', map_modal ).animate({
                left: 0,
            }, delay, function () {

            });
            var right = $( '.media-sidebar' ).width();
            $( '.attachments', map_modal ).animate({
                left: right,
            }, delay, function () {

            });
        }
        map_modal.on( 'click', '.locations-close', function( e ){
            e.preventDefault();
            closeLocationsSidebar();
        } );

        function idGenerator() {

            this.length = 8;
            this.timestamp = +new Date;

            var _getRandomInt = function( min, max ) {
                return Math.floor( Math.random() * ( max - min + 1 ) ) + min;
            };

            var ts = this.timestamp.toString();
            var parts = ts.split( "" ).reverse();
            var id = "";

            for( var i = 0; i < this.length; ++i ) {
                var index = _getRandomInt( 0, parts.length - 1 );
                id += parts[index];
            }

            return id;
        }


        function setAnimation( data, animation ){
            $.each( locations,function ( index ){
                if ( locations[ index ]._marker ) {
                    locations[ index ]._marker.setAnimation( null );
                    locations[ index ]._marker.setDraggable( false );
                }
                if ( locations[ index ]._infowindow ) {
                    locations[ index ]._infowindow.close( );
                }

            } );
            if ( data._marker ) {
                data._marker.setAnimation( animation );
                data._infowindow.open( data._infowindow, data._marker );
            }
        }

        function setMarkerIcon( marker, options ){
            var settings = $.extend( {}, {
                url: '',
                w: 0,
                h: 0,
                scale_size: 30,
                scale_w: 0,
                scale_h: 0,
            }, options );

            if ( ! marker || ! settings.url ) {
                marker.setIcon( null );
                return ;
            }

            settings.w = mapifyFomat.toInt( settings.w );
            settings.h = mapifyFomat.toInt( settings.h );

            if ( settings.w > settings.scale_size && settings.w ) {
                settings.scale_w = settings.scale_size;
                settings.scale_h = ( settings.scale_w / settings.w )*settings.h;
            } else {
                settings.scale_w = settings.w;
                settings.scale_h = settings.h;
            }

            var icon = {
                url: settings.url, // url
               // size: new google.maps.Size(settings.w, settings.h),
                scaledSize: new google.maps.Size(settings.scale_w, settings.scale_h), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point( settings.scale_w/2, settings.scale_h) // anchor
            };
            marker.setIcon( icon );
        }

        function setLocationMarker( data ){

            if ( data.latitude.toString().length > 0 && data.longitude.toString().length ) {

                data.latitude = parseFloat(data.latitude);
                data.longitude = parseFloat(data.longitude);
                var marker = new google.maps.Marker({
                    position: {
                        lat: data.latitude,
                        lng: data.longitude
                    },
                    map: gmap,
                    draggable: true,
                });

                data._marker = marker;

                // Set maker icon
                setMarkerIcon( data._marker, {
                    url: data.marker,
                    w: data.marker__width,
                    h: data.marker__height,
                } );

                google.maps.event.addListener(data._marker, 'drag', function () {
                    data._li.find('[name="latitude"]').val(data._marker.getPosition().lat());
                    data._li.find('[name="longitude"]').val(data._marker.getPosition().lng());
                    data._li.find('[name="latitude"], [name="longitude"]').trigger('data_changed');
                    toggleGroupSettings(data._li, 'open');
                    if (typeof window.marker_timeout === "undefined") {
                        window.marker_timeout = null;
                    }

                    if (window.marker_timeout) {
                        clearTimeout(window.marker_timeout);
                    }

                    window.marker_timeout = setTimeout(function () {
                        $('.locations-sidebar', map_modal).scrollToEl(data._li);
                    }, delay);

                });

                google.maps.event.addListener(data._marker, 'click', function () {
                    // Open location sidebar
                    toggleGroupSettings( data._li, 'open' );
                    toggleLocationsSidebar( 'open' );

                    if (typeof window.marker_timeout === "undefined") {
                        window.marker_timeout = null;
                    }

                    if (window.marker_timeout) {
                        clearTimeout(window.marker_timeout);
                    }

                    window.marker_timeout = setTimeout(function () {
                        $('.locations-sidebar', map_modal).scrollToEl(data._li);
                    }, delay);

                });

                data._li.on( 'opened', function( e ){
                    e.preventDefault();
                    if ( data._marker ) {
                        setAnimation( data, google.maps.Animation.BOUNCE );
                        data._marker.setDraggable( true );
                    }

                    gmap.setCenter({
                        lat: data.latitude,
                        lng: data.longitude
                    });
                } );

                data._li.on( 'closed', function( e ){
                    e.preventDefault();
                    if ( data._marker ) {
                        setAnimation( data, null );
                    }
                } );

                // infowindow
                var info = getTemplate( 'mapify-infowindow-template', data );
                data._infowindow = new google.maps.InfoWindow({
                    content: info
                });
                data._info = $( info );

                data._marker.addListener('click', function() {
                    data._infowindow.open( data._infowindow, data._marker);
                });

            }// and if set lat & lng
        }

        function setupLocation( data ) {
            data = $.extend({}, {
                map_id: null,
                location_id: null,
                latitude: '',
                longitude: '',
            }, data);
            if ( ! data.location_id ) {
                data.location_id = 'new-' + idGenerator();
            }

            var li;
            li = $( getTemplate('mapify-location-li', data ) );
            if ( locations[ data.location_id ] && locations[ data.location_id]._li ) {
                locations[ data.location_id]._li.replaceWith( li );
            } else {
                $('.locations-list', map_modal).append(li);
            }

            data._li = li;

            setLocationMarker( data );

            // Search
            var autocomplete = new google.maps.places.Autocomplete( $('[name="address"]', data._li )[0] );
            autocomplete.bindTo( 'bounds', gmap );

            google.maps.event.addListener( autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                if ( ! place.geometry ) {
                    return;
                }

                if ( place.geometry.viewport ) {
                    gmap.fitBounds( place.geometry.viewport );
                } else {
                    gmap.setCenter( place.geometry.location );
                    gmap.setZoom( 17 );
                }

                // marker.setPosition( place.geometry.location );
                data.latitude  = place.geometry.location.lat();
                data.longitude = place.geometry.location.lng();

                setLocationMarker( data );
                if ( data._marker ) {
                    setAnimation(data, google.maps.Animation.BOUNCE);
                    data._marker.setDraggable(true);
                }

                data._li.find('[name="latitude"]').val( data.latitude );
                data._li.find('[name="longitude"]').val(data.longitude );
                data._li.find('[name="latitude"], [name="longitude"], [name="address"]').trigger('data_changed');

            });

            $('[name="address"]', data._li ).keypress( function( event ) {
                if ( 13 === event.keyCode ) {
                    event.preventDefault();
                }
            });


            // When data changed
            data._li.on( 'keyup change data_changed', 'input, select, textarea' , function( e ){
                var input = $( this );
                var name = input.attr( 'name' );
                var value = getInputVal( input );
                changeLocationData( data.location_id, name, value );

                // Live update title
                if ( name == 'title' ) {
                    data._li.find( '.map-og-heading').text( value != '' ? value : mapify_config.untitled );
                }

            });

            locations[ data.location_id ] = data;

            return data.location_id;
        }

        function changeLocationData( location_id, key, value ){
            if ( typeof data_changed.locations[ location_id ] === "undefined" ) {
                data_changed.locations[ location_id ] = {};
            }
            data_changed.locations[ location_id ][ key ] = value;

            switch ( key ){
                case 'marker':

                    if ( locations[ location_id ]._marker ) {
                        var w = locations[ location_id]._li.find( '[name="'+( key )+'__width"]').val();
                        var h = locations[ location_id]._li.find( '[name="'+( key )+'__height"]').val();
                        w = mapifyFomat.toInt( w );
                        h = mapifyFomat.toInt( h );

                        setMarkerIcon( locations[ location_id ]._marker, {
                            url: value,
                            w: w,
                            h: h,
                        } );

                    }

                    break;
            }
            enableSaveData();
            //$('#js-debug').text( JSON.stringify( data_changed ) );
        }

        function changeMapData( key, value ){
            data_changed.map[ key ] = value;
            // Live preview
            switch ( key ){
                case 'pan_controller':
                    gmap.setOptions( { panControl: mapifyFomat.toBool( value ) });
                    break;
                case 'wheel_scrolling':
                    gmap.setOptions( { scrollwheel: mapifyFomat.toBool( value ) });
                    break;
                case 'zoom_controller':
                    gmap.setOptions( { zoomControl: mapifyFomat.toBool( value ) });
                    break;
                case 'street_view_controller':
                    gmap.setOptions( { streetViewControl: mapifyFomat.toBool( value ) });
                    break;
                case 'map_type_controller':
                    gmap.setOptions( { mapTypeControl: mapifyFomat.toBool( value ) });
                    break;
                case 'scale_controller':
                    gmap.setOptions( { scaleControl: mapifyFomat.toBool( value ) });
                    break;
                case 'map_draggable':
                    gmap.setOptions( { draggable: mapifyFomat.toBool( value ) });
                    break;
                case 'map_type':
                    switch ( value ) {
                        case 'HYBRID':
                            gmap.setMapTypeId( 'hybrid' );
                            break;
                        case 'SATELLITE':
                            gmap.setMapTypeId( 'satellite' );
                            break;
                        case 'TERRAIN':
                            gmap.setMapTypeId( 'terrain' );
                            break;
                        default :
                            gmap.setMapTypeId( 'roadmap' );

                    }

                    break;
            }

            enableSaveData();
           // $('#js-debug').text( JSON.stringify( data_changed ) );
        }

        /**
         * Adds a marker to the map and push to the array.
         *
         * @param location
         * @param map
         */
        function addMarker(location ) {
            var data = {
                latitude: '',
                longitude: '',
            };
            if ( typeof location !== "undefined" ) {
                data = {
                    latitude: location.lat(),
                    longitude: location.lng(),
                };
            }
            var l_id = setupLocation( data );
            if (  locations[ l_id ]._marker ) {
                setAnimation(locations[l_id], google.maps.Animation.BOUNCE);
            }
            // Open location sidebar
            toggleGroupSettings( locations[ l_id ]._li, 'open' );
            toggleLocationsSidebar( 'open' );

            locations[ l_id ]._li.find( '[name="latitude"], [name="longitude"]' ).trigger( 'data_changed' );

            if ( typeof window.marker_timeout === "undefined" ) {
                window.marker_timeout = null;
            }

            if (  window.marker_timeout ) {
                clearTimeout(  window.marker_timeout );
            }

            window.marker_timeout = setTimeout( function(){
                $( '.locations-sidebar', map_modal ).scrollToEl( locations[ l_id ]._li );
            }, delay );
        }


        function updateViewPort(){
            var center = gmap.getCenter();
            $( 'input[name="center_latitude"]', map_modal ).val( center.lat() );
            $( 'input[name="center_longitude"]', map_modal ).val( center.lng() );
            $( 'input[name="center_longitude"]', map_modal ).val( center.lng() );
            $( 'input[name="zoom_level"]', map_modal ).val( gmap.getZoom() );

            changeMapData( 'center_latitude', center.lat() );
            changeMapData( 'center_longitude', center.lng() );
            changeMapData( 'zoom_level', gmap.getZoom() );
        }


        /**
         * Preview Map
         */
        $( '.map-preview', map_modal ).each( function(){
            var center;
            var data = map_data;
            try {
                if ( data.center_latitude.toString().length > 0 && data.center_longitude.toString().length > 0 ) {
                    center = new google.maps.LatLng( mapifyFomat.toFloat( data.center_latitude ) , mapifyFomat.toFloat( data.center_longitude ) );
                } else {
                    center = new google.maps.LatLng( 54.800685, -4.130859 );
                }
            } catch ( e ) {
                center = new google.maps.LatLng( 54.800685, -4.130859 );
            }

            data.zoom_level = mapifyFomat.toInt( data.zoom_level );
            if ( data.zoom_level == 0 ) {
                data.zoom_level = 12;
            }

            var mapOptions = {
                center: center,
                zoom: data.zoom_level,
                disableDoubleClickZoom: false,
                scrollwheel: mapifyFomat.toBool( data.wheel_scrolling ),
                panControl: mapifyFomat.toBool( data.pan_controller ),
                zoomControl: mapifyFomat.toBool( data.zoom_controller ),
                streetViewControl: mapifyFomat.toBool( data.street_view_controller ),
                mapTypeControl: mapifyFomat.toBool( data.map_type_controller ),
                scaleControl: mapifyFomat.toBool( data.scale_controller ),
                draggable: mapifyFomat.toBool( data.map_draggable ),
                //language: 'en',
            };
            switch ( data.map_type ) {
                case 'HYBRID':
                    mapOptions.mapTypeId = 'hybrid';
                    break;
                case 'SATELLITE':
                    mapOptions.mapTypeId = 'satellite';
                    break;
                case 'TERRAIN':
                    mapOptions.mapTypeId = 'terrain';
                    break;
                default :
                    mapOptions.mapTypeId = 'roadmap';
            }


            gmap = new google.maps.Map( $( this )[0], mapOptions );
            gmap.addListener('rightclick', function( event ) {
               // if ( mapify.current_action == 'new_marker' ) {
                   // addMarker( event.latLng, gmap );
                   // mapify.current_action = null;
               // }
            });
        } );

        map_modal.on( 'click', '.location-actions a', function( e ){
            e.preventDefault();
            var action = $( this).attr( 'data-action' );
            mapify.current_action = action;
            addMarker( );
            //$( '.action-msg', map_modal ).html( mapify_config.helps.new_marker );
        } );

        // When data changed
        $( '.mapify-map-form', map_modal).on( 'keyup change data_changed', 'input, select, textarea' , function( e ){
            var input = $( this );
            var value = getInputVal( input );
            var name = input.attr( 'name' );
            changeMapData( name, value );
        });

        /**
         * When map title changed
         */
        $( '.map-title', map_modal).on( 'change keyup', function(){
            changeMapData( 'map_title',  $( this).html() );
        } );


        // Close Modal

        map_modal.on( 'click', '.media-modal-close', function( e ) {
            e.preventDefault();
            $( '.media-modal-backdrop').remove();
            map_modal.remove();
            $( 'body').trigger( 'map_modal_closed' );
            delete data_changed;
            delete locate_tpl;
            delete gmap;
            delete locations;
        } );

        // Save Changes
        $( '.mapify-save', map_modal).on( 'click', function( e ){
            e.preventDefault();
            // Just Update map title
            changeMapData( 'map_title', $( '.map-title', map_modal ) .html() );
            data_changed.action = 'mapify_save';
            data_changed._nonce =   mapify_config.nonce;
            try {
                if ( ! data_changed.map.center_latitude || ! data_changed.map.center_longitude || ! data_changed.map.zoom_level  ) {
                    updateViewPort();
                }
            } catch ( e ) {

            }
            disableSaveData();
            map_modal.addClass( 'saving' );
            $( '.mapify-save', map_modal).html( mapify_config.saving );
            $.ajax( {
                url: mapify_config.ajax_url,
                type: 'POST',
                data: data_changed,
                cache: false,
                dataType: 'json',
                success: function ( res ) {
                    map_modal.removeClass( 'saving' );
                    $( '.mapify-save', map_modal).html( mapify_config.save_changes );
                    if ( res.success ) {
                        // Update map_id
                        data_changed.map_id = res.data.map_id;

                        // Reset changed data
                        data_changed = {
                            map: {
                                map_id: res.data.map_id
                            },
                            locations: {},
                            map_id: res.data.map_id
                        };

                        changeMapData( 'map_id', res.data.map_id );
                        $.each( res.data.locations, function ( new_id, id ){
                            changeLocationData( new_id, 'location_id', id );
                        } );
                        disableSaveData();
                        if ( res.data.is_new ) {
                            appendMapToList( res.data.map_data );
                        }
                    }
                }
            } );
        } );

        // setup location if have
        if ( map_locations ) {
            $.each( map_locations, function( id, locate ){
                setupLocation( locate );
            } );
        }

        // Delete location
        map_modal.on( 'click', '.del-location', function( e ){
            e.preventDefault();
            var c = confirm( mapify_config.confirm );
            if ( c ) {
                var location_id = $(this).attr('data-l-id') || false;
                $.ajax({
                    url: mapify_config.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'mapify_del_location',
                        _nonce: mapify_config.nonce,
                        location_id: location_id
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            try {
                                if (location_id && locations[location_id]) {
                                    if (locations[location_id]._li) {
                                        locations[location_id]._li.remove();
                                    }

                                    if (locations[location_id]._marker) {
                                        locations[location_id]._marker.setMap(null);
                                    }
                                    delete locations[location_id];
                                }
                            } catch (e) {

                            }
                        }
                    }
                });
            } // end confirm
        }); // end del


        // Set map center
        map_modal.on( 'click', '.mapify-center', function( e ){
            e.preventDefault();
            updateViewPort();

        });

        window.mapify_tooltip = false;

        map_modal.on( 'mousemove', '.field-input .dashicons-editor-help', function( e ){
            var f = $( this).closest( '.field-input' );
            var icon = $( this );
            var h, w, t, l;
            if ( window.mapify_tooltip ) {
                h = window.mapify_tooltip.outerHeight();
                w = window.mapify_tooltip.outerWidth();
                l = e.pageX - w / 2;
                if ( l <= 0 ) {
                    l = 0;
                }
                window.mapify_tooltip.css({
                    top: e.pageY - ( h + icon.height() ),
                    left: l
                });
            } else {
                t = f.find('.help-tooltip');
                window.mapify_tooltip = t.clone();
                $('body').append(window.mapify_tooltip);
                window.mapify_tooltip.css({
                    top: e.pageY + 15,
                    left: e.pageX,
                    display: 'block',
                });

                h = t.outerHeight();
                w = t.outerWidth();
                l = e.pageX - w / 2;
                if ( l <= 0 ) {
                    l = 0;
                }
                window.mapify_tooltip.css({
                    top: e.pageY - ( h + icon.height() ),
                    left: l,
                });
            }

        } );

        // Help tooltip
        map_modal.on( 'mouseleave', '.field-input .dashicons-editor-help', function( e ){
            window.mapify_tooltip.remove();
            window.mapify_tooltip = false;
        } );

        // Media upload

        var frame = wp.media({
            title: wp.media.view.l10n.addMedia,
            multiple: false,
            library: {type: 'image' },
            //button : { text : 'Insert' }
        });

        var media_current;

        frame.on('close', function () {
            // get selections and save to hidden input plus other AJAX stuff etc.
            var selection = frame.state().get('selection');
            // console.log(selection);
        });

        frame.on( 'select', function () {
            // Grab our attachment selection and construct a JSON representation of the model.
            var media_attachment = frame.state().get('selection').first().toJSON();
            var preview, img_url;
            if ( media_attachment.type != 'video' ) {

                if ( media_attachment.sizes.thumbnail ) {
                    img_url = media_attachment.sizes.thumbnail.url;
                    $( '.media_id', media_current  ).val( media_attachment.id );
                    $( '.media_type', media_current ).val( media_attachment.type );
                    $( '.media_url', media_current  ).val( img_url );
                    $( '.media_size_width', media_current  ).val( media_attachment.sizes.thumbnail.width );
                    $( '.media_size_height', media_current  ).val( media_attachment.sizes.thumbnail.height );

                } else {
                    img_url = media_attachment.url;
                    $( '.media_id', media_current  ).val( media_attachment.id );
                    $( '.media_type', media_current ).val( media_attachment.type );
                    $( '.media_url', media_current  ).val( img_url );
                    $( '.media_size_width', media_current  ).val( media_attachment.width );
                    $( '.media_size_height', media_current  ).val( media_attachment.height );
                }

            } else {
                img_url = media_attachment.url;
                $( '.media_id', media_current  ).val( media_attachment.id );
                $( '.media_type', media_current ).val( media_attachment.type );
                $( '.media_url', media_current  ).val( img_url );
                $( '.media_size_width', media_current  ).val( '' );
                $( '.media_size_height', media_current  ).val( '' );
            }


            media_current.addClass( 'has-preview' );
            if ( media_attachment.type == 'video' ) {

                preview = '<video width="400" controls>'+
                    '<source src="'+img_url+'" type="'+media_attachment.mime+'">'+
                    'Your browser does not support HTML5 video.'+
                    '</video>';
                $('.media-preview', media_current  ).html(preview);

            } else if ( media_attachment.type == 'image' ) {
                preview = '<img src="' + img_url + '" alt="">';
                $('.media-preview', media_current  ).html(preview);
            }

            $('.media-remove', media_current  ).show();
            $( '.media_id, .media_type, .media_url, .media_size_width, .media_size_height', media_current  ).trigger( 'change' );

        });

        map_modal.on( 'click', '.media-upload .media-preview', function( e ){
            e.preventDefault();
            media_current = $( this).closest( '.media-upload' );
            frame.open();
        } );

        map_modal.on( 'click', '.media-upload .media-remove', function( e ){
            e.preventDefault();
            media_current = $( this).closest( '.media-upload' );
            $('.media-preview', media_current).html( '' );
            $( '.media_id, .media_type, .media_url', media_current  ).val( '' );
            $( '.media_id, .media_type, .media_url, .media_size_width, .media_size_height', media_current  ).trigger( 'change' );
            media_current.removeClass( 'has-preview' );

        } );


        // end upload


    } // End

    MapifyAdminControler();


}( jQuery, window ));
