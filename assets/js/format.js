
window.mapifyFomat = {
    toFloat: function( test ){
        var n = parseFloat( test );
        if ( isNaN( n ) ){
            return 0;
        } else {
            return n;
        }
    },
    toInt: function( test ){
        var n = parseInt( test );
        if ( isNaN( n ) ){
            return 0;
        } else {
            return n;
        }
    },
    toBool: function ( test ) {
        if ( 'boolean' === typeof test ) {
            return test;
        }
        if ( 'string' === typeof test ){
            return ( 'true' == test || '1' == test ) ? true : false;
        } else {
            return ( this.toInt( test ) == 1 ) ? true : false;
        }
    },
    toCssUnit: function test( test ) {
        var css = {
            n: false,
            u: false
        };
        if ( typeof test === 'number' ) {
            css.n = test;
            css.u = 'px';
        } else if ( test.length > 0 ) {
            css.n = this.toFloat( test );
            if ( test.toLowerCase().indexOf( '%' ) > -1 ) { // percent
                css.u = '%';
            } else {
                css.u = 'px';
            }
        }
        return css;
    }
};