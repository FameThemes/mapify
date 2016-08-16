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
        }, 0 );
        return this;
    };
}( jQuery, window ));

var mapify = {
    current_action: null,
};


( function( $, window ) {

    function mapifyAdmin(){
        var delay = 400, markers = [], locations = {}, gmap, map_modal;

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


        map_modal = $( getTemplate( 'mapify-map-template' ) );
        $( 'body' ).append( map_modal );
        $( 'body' ).append( '<div class="media-modal-backdrop"></div>' );

        // Insert locations
        var locate_tpl =  $( getTemplate( 'mapify-locations-template' ) );
        $( '.attachments-browser', map_modal ).append( locate_tpl );

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
                }, delay, function () {

                    $('.attachments', map_modal).animate({
                        left: locate_tpl.width() + right,
                    }, delay, function () {
                        if ( typeof cb == "function" ) {
                            cb();
                        }
                    });
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
        map_modal.on( 'click', '.location-close', function( e ){
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

        function setAnimation( marker, animation ){
            $.each( locations,function ( index ){
                locations[ index ]._marker.setAnimation( null );
            } );

            marker.setAnimation( animation );
        }

        function setupLocation( data ){
            data = $.extend( {}, {
                map_id:      null,
                location_id: null,
                latitude: 0,
                longitude: 0,
            }, data );

            data.latitude = parseFloat( data.latitude );
            data.longitude = parseFloat( data.longitude );

            if ( ! data.location_id ) {
                data.location_id = 'new-'+idGenerator();
            }
            var li = $( getTemplate( 'mapify-location-li', data ) );
            var marker = new google.maps.Marker({
                position: {
                    lat: data.latitude,
                    lng: data.longitude
                },
                map: gmap,
                draggable: true,
            });

            data._marker = marker;
            data._li = li;
            $( '.locations-list', map_modal ).append( li );

            google.maps.event.addListener( data._marker, 'drag', function() {
                data._li.find( '[name="latitude"]' ).val( data._marker.getPosition().lat() );
                data._li.find( '[name="longitude"]' ).val( data._marker.getPosition().lng() );
                setAnimation( data._marker, google.maps.Animation.BOUNCE );
                toggleGroupSettings( data._li, 'open' );

                if ( typeof window.marker_timeout === "undefined" ) {
                    window.marker_timeout = null;
                }

                if (  window.marker_timeout ) {
                    clearTimeout(  window.marker_timeout );
                }

                window.marker_timeout = setTimeout( function(){
                    $( '.locations-sidebar', map_modal ).scrollToEl( data._li );
                }, delay );

            });

            google.maps.event.addListener( data._marker, 'click', function() {
                setAnimation( data._marker, google.maps.Animation.BOUNCE );
                toggleGroupSettings( data._li, 'open' );

                if ( typeof window.marker_timeout === "undefined" ) {
                    window.marker_timeout = null;
                }

                if (  window.marker_timeout ) {
                    clearTimeout(  window.marker_timeout );
                }

                window.marker_timeout = setTimeout( function(){
                    $( '.locations-sidebar', map_modal ).scrollToEl( data._li );
                }, delay );

            });

            data._li.on( 'opened', function( e ){
                e.preventDefault();
                setAnimation( data._marker, google.maps.Animation.BOUNCE );
            } );

            data._li.on( 'closed', function( e ){
                e.preventDefault();
                setAnimation( data._marker, null );
            } );

            locations[ data.location_id ] = data;

            return data.location_id;
        }

        /**
         * Adds a marker to the map and push to the array.
         *
         * @param location
         * @param map
         */
        function addMarker(location ) {
            var data = {
                latitude: location.lat(),
                longitude:  location.lng(),
            };
            var l_id = setupLocation( data );
            setAnimation( locations[ l_id ]._marker, google.maps.Animation.BOUNCE );
            toggleGroupSettings( locations[ l_id ]._li, 'open' );
            toggleLocationsSidebar( 'open' );

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

        /**
         * Preview Map
         */
        $( '.map-preview', map_modal ).each( function(){
            // Map
            var mapOptions = {
                center: new google.maps.LatLng( 54.800685, -4.130859 ),
                zoom: 12,
                disableDoubleClickZoom: true,
                panControl: true,
            };
            gmap = new google.maps.Map( $( this )[0], mapOptions );
            gmap.addListener('rightclick', function( event ) {
               // if ( mapify.current_action == 'new_marker' ) {
                    addMarker( event.latLng, gmap );
                    mapify.current_action = null;
               // }
            });
        } );

        map_modal.on( 'click', '.new-marker', function( e ){
            e.preventDefault();
            mapify.current_action = 'new_marker';
            $( '.action-msg', map_modal ).html( mapify_config.helps.new_marker );
        } );

    }

    window.mapifyAdmin = mapifyAdmin();

}( jQuery, window ));
