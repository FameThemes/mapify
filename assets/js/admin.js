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


//---------------------------------------------------
function get_template( tmpl_id, data ){
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


jQuery(  document ).ready( function ( $ ) {



    $( 'body' ).on( 'click', '.map-option-group li .map-og-heading', function( e ){
        e.preventDefault();
        var li = $( this ).closest( 'li' );
        if ( ! li.hasClass( 'opened' ) ) {
            $( '.map-option-group' ).find( 'li' ).removeClass( 'opened' ).find( '.map-og-settings' ).slideUp(400);
            li.addClass( 'opened' ).find( '.map-og-settings' ).slideDown(400);
        } else {
            li.removeClass( 'opened' ).find( '.map-og-settings' ).slideUp(400);
        }
    } );

    var modal = get_template( 'mapify-map-template' );
    $( 'body' ).append( modal );
    $( 'body' ).append( '<div class="media-modal-backdrop"></div>' );
    var delay = 400;

    $( 'body' ).on( 'click', '.locations li', function( e ){
        e.preventDefault();
        var locate_tpl =  $( get_template( 'mapify-location-template' ) );
        $( '.attachments-browser' ).append( locate_tpl );
        var right = $( '.media-sidebar' ).width();


        if (  $( this ).hasClass( 'active' ) ) {
          return false;
        } else {
          $( '.locations li' ).removeClass( 'active' );
          $( this ).addClass( 'active' );
        }

        locate_tpl.animate({
           right: right,
        }, delay, function () {

           $('.map-preview').animate({
               right: locate_tpl.width() + right,
           }, delay, function () {

           });
           $( '.location-sidebar' ).not( locate_tpl ).remove();

           setTimeout( function () {
               $( '.map-option-group', locate_tpl ).find( 'li' ).removeClass( 'opened' ).find( '.map-og-settings' ).slideUp( delay );
               $( '.map-option-group', locate_tpl ).find( 'li' ).eq(0).addClass( 'opened' ).find( '.map-og-settings' ).slideDown( delay );

           }, delay );

        });

    } );

    $( 'body' ).on( 'click', '.location-close', function( e ){
        $( '.location-sidebar' ).animate({
            right: 0,
        }, delay, function () {
            $( '.location-sidebar' ).remove();
        });

        $( '.locations li' ).removeClass( 'active' );

        var right = $( '.media-sidebar' ).width();
        $( '.map-preview' ).animate({
            right: right,
        }, delay, function () {

        });

    } );


    //Preview Map
    // Adds a marker to the map and push to the array.
    var markers = [];
    function addMarker(location, map) {
        var marker = new google.maps.Marker({
            position: location,
            map: gmap
        });
        markers.push(marker);
    }

    $( '.map-preview' ).each( function(){
        // Map
        var mapOptions = {
            center: new google.maps.LatLng( 54.800685, -4.130859 ),
            zoom: 12,
            disableDoubleClickZoom: true,
            panControl: true,
        };
        gmap = new google.maps.Map( $( this )[0], mapOptions );


        gmap.addListener('click', function(event) {
            console.log( event );
            addMarker(event.latLng, gmap );
        });


    } );






} );