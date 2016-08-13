<?php
class Mapify_Location {
    public function insert(  $args, $map_id =  0 ) {
        $args = wp_parse_args( $args, array(
            'location_id'           => '',
            'map_id'                => '',
            'location_title'        => '',
            'location_address'      => '',
            'location_latitude'     => '',
            'location_longitude'    => '',
            'location_state'        => '',
            'location_city'         => '',
            'location_country'      => '',
            'location_postal_code'  => '',
            'location_on_click'     => '',
            'location_redirect_url' => '',
            'location_info'         => '',
            'location_marker'       => '',
            'location_cat'          => '',
        ) );

        $post_data = array();
        $post_data['post_title']    = trim( $args['location_title'] ) ? $args['location_title'] : $args['location_address'];
        $post_data['post_content']  = $args['location_info'];
        $post_data['post_type']     = 'location';
        $post_data['post_status']   = 'publish';

        $post_meta = array(
            'location_address',
            'location_latitude',
            'location_longitude',
            'location_state',
            'location_postal_code',
            'location_city',
            'location_country',
            'location_redirect_url',
            'location_marker',
            'map_id',
        );


        if ( $map_id ) {
            $post_data['post_parent']   = $map_id;
            $args['map_id'] = $map_id;
        } else {
            $post_data['post_parent']   = $args['map_id'];
        }

        $post_id = null;
        if ( $args['location_id'] > 0 ) {
            $post = get_post( $args['location_id'] );
            if ( $post ) {
                $post_id =  $post->ID;
            }
        }

        if ( $post_id > 0 ) {
            $post_data['ID'] =  $post_id;
        }

        $l_id = wp_insert_post( $post_data );

        if ( $l_id && ! is_wp_error( $l_id ) ) {
            foreach ( $post_meta as $meta_key ) {
                $val = isset( $args[ $meta_key ] ) ? $args[ $meta_key ] : '';
                update_post_meta( $l_id, '_'.$meta_key, $val );
            }
        } else {
            $l_id = false;
        }

        return $l_id;
    }

    public function update( $args = array(), $map_id = '' ){
        return self::insert( $args, $map_id );
    }

    function get_location( $p ){
        $p = get_post( $p );
        if ( ! $p ) {
            return false;
        }
        $location = array(
            'location_id'  => $p->ID,
            'map_id'       => get_post_meta( $p->ID, '_map_id', true ),
            'title'        => $p->post_title,
            'address'      => get_post_meta( $p->ID, '_location_address', true ),
            'latitude'     => get_post_meta( $p->ID, '_location_latitude', true ),
            'longitude'    => get_post_meta( $p->ID, '_location_longitude', true ),
            'state'        => get_post_meta( $p->ID, '_location_state', true ),
            'city'         => get_post_meta( $p->ID, '_location_city', true ),
            'country'      => get_post_meta( $p->ID, '_location_country', true ),
            'postal_code'  => get_post_meta( $p->ID, '_location_postal_code', true ),
            'on_click'     => get_post_meta( $p->ID, '_location_on_click', true ),
            'redirect_url' => get_post_meta( $p->ID, '_location_redirect_url', true ),
            'info'         => $p->post_content,
            'marker'       => get_post_meta( $p->ID, '_location_marker', true ),
            'cat'          => get_post_meta( $p->ID, '_location_cat', true ),
        );
        return $location;
    }

    function get_locations( $args = array(), &$query = false ){
        $args = wp_parse_args( $args, array(
            'posts_per_page' => 100
        ) );
        if ( isset ( $args['map_id'] ) && $args['map_id'] > 0 ) {
            $args['post_parent'] = $args['map_id'];
            unset( $args['map_id'] );
        }
        $args['post_type'] = 'location';
        $query = new WP_Query( $args );
        $locations = array();
        foreach ( ( array ) $query->get_posts() as $p ) {
            $locations[ $p->ID ] = $this->get_location( $p );;
        }
        return $locations;
    }

}

function Mapify_Location(){
    if ( ! isset( $GLOBALS['Mapify_Location'] ) ) {
        $GLOBALS['Mapify_Location'] = new Mapify_Location();
    }
    return $GLOBALS['Mapify_Location'] ;
}