

jQuery( document).ready( function( $ ){
    window.mapify_maps = {};

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


    function setLocationMarker( data, map ){

        if ( data.latitude.toString().length > 0 && data.longitude.toString().length ) {

            data.latitude = mapifyFomat.toFloat(data.latitude);
            data.longitude = mapifyFomat.toFloat(data.longitude);
            var marker = new google.maps.Marker({
                position: {
                    lat: data.latitude,
                    lng: data.longitude
                },
                map: map,
                draggable: false,
            });

            data._marker = marker;
            google.maps.event.addListener(data._marker, 'click', function () {

            });

            // Infowindow
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


    $( '.mapify').each( function(){
        var map_wrapper = $( this );
        var map_id = map_wrapper.attr( 'data-map-id' ) || 0;
        $.ajax({
            url: Mapify.ajax_url,
            type: 'POST',
            data: {
                action: 'mapify_load_map',
                _nonce: Mapify.nonce,
                map_id: map_id
            },
            cache: false,
            dataType: 'json',
            success: function (res) {
                if ( res.success ) {
                    var data = res.data.map;
                    if ( 'string' === typeof data ) {
                        data = JSON.parse(data);
                    }

                    data.center_latitude = mapifyFomat.toFloat( data.center_latitude );
                    data.center_longitude = mapifyFomat.toFloat( data.center_longitude );
                    data.zoom_level = mapifyFomat.toFloat( data.zoom_level );
                    if ( data.zoom_level == 0 ) {
                        data.zoom_level = 12;
                    }

                    var mapOptions = {
                        center: new google.maps.LatLng( data.center_latitude, data.center_longitude ),
                        zoom: data.zoom_level,
                        disableDoubleClickZoom: false,
                        scrollwheel: mapifyFomat.toBool( data.wheel_scrolling ),
                        panControl: mapifyFomat.toBool( data.pan_controller ),
                        zoomControl: mapifyFomat.toBool( data.zoom_controller ),
                        streetViewControl: mapifyFomat.toBool( data.street_view_controller ),
                        mapTypeControl: mapifyFomat.toBool( data.map_type_controller ),
                        scaleControl: mapifyFomat.toBool( data.scale_controller ),
                        draggable: mapifyFomat.toBool( data.map_draggable ),
                        maxZoom: mapifyFomat.toBool( data.zoom_max ),
                        minZoom: mapifyFomat.toBool( data.zoom_min ),
                        //mapTypeId: data.map_type == '' ? 'ROADMAP' : data.map_type ,
                        //language: 'en',
                    };
                    if ( mapOptions.maxZoom <= 0 ) {
                        delete mapOptions.maxZoom;
                    }
                    if ( mapOptions.minZoom <= 0 ) {
                        delete mapOptions.minZoom;
                    }
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

                    // set map width/ height
                    var cssW = mapifyFomat.toCssUnit( data.map_width );
                    var cssH = mapifyFomat.toCssUnit( data.map_height );
                    if ( cssW.u ) {
                        map_wrapper.css( {
                            width: cssW.n + cssW.u
                        } );
                    }
                    if ( cssH.u ) {
                        map_wrapper.css( {
                            height: 0,
                            display: 'block',
                            paddingTop: cssH.n + cssH.u
                        } );
                    }

                    mapify_maps[ data.map_id] = {};
                    mapify_maps[ data.map_id].data = data;
                    mapify_maps[ data.map_id].map = new google.maps.Map( map_wrapper.find('.mapify-gmap')[0], mapOptions );
                    //Traffic layer
                    if ( mapifyFomat.toBool( data.traffic_layer ) ) {
                        var trafficLayer = new google.maps.TrafficLayer();
                        console.log( 'traffic_layer');
                        trafficLayer.setMap( mapify_maps[ data.map_id].map );
                    }
                    // transit layer
                    if ( mapifyFomat.toBool( data.transit_layer ) ) {
                        var transitLayer = new google.maps.TransitLayer();
                        console.log( 'transitLayer');
                        transitLayer.setMap( mapify_maps[ data.map_id].map );
                    }
                    // bicycling layer
                    if ( mapifyFomat.toBool( data.bicycling_layer ) ) {
                        var bikeLayer = new google.maps.BicyclingLayer();
                        console.log( 'bikeLayer');
                        bikeLayer.setMap( mapify_maps[ data.map_id].map );
                    }

                    mapify_maps[ data.map_id].locations = {};

                    $.each( res.data.locations, function( l_id, location_data ){
                        setLocationMarker( location_data, mapify_maps[ data.map_id].map );
                        mapify_maps[ data.map_id].locations[ l_id ] = location_data;
                    } );

                }
            }
        });


    } );
} );
