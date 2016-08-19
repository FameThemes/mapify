<?php
class Mapify_Location {
    public function insert(  $args, $map_id = 0 ) {
        $args = wp_parse_args( $args, array(
            'location_id'  => '',
            'map_id'       => '',
            /*
            'title'        => '',
            'address'      => '',
            'latitude'     => '',
            'longitude'    => '',
            'state'        => '',
            'city'         => '',
            'country'      => '',
            'postal_code'  => '',
            'on_click'     => '',
            'redirect_url' => '',
            'info'         => '',
            'marker'       => '',
            */
        ) );

        $post_id = null;
        if ( $args['location_id'] > 0 ) {
            $post = get_post( $args['location_id'] );
            if ( $post ) {
                $post_id =  $post->ID;
            }
        }

        if ( ! $post_id || isset( $args['title'] ) || isset( $args['info'] ) ) {
            $post_data = array();
            if ( isset( $args['title'] ) ) {
                $post_data['post_title'] = trim( $args['title'] );
            }
            if ( isset( $args['info'] ) ) {
                $post_data['post_content'] = $args['info'];
            }
            $post_data['post_type'] = 'location';
            $post_data['post_status'] = 'publish';

            if ( $post_id ) {
                $post_data['ID'] = $post_id;
            }

            if ( $map_id ) {
                $post_data['post_parent']   = $map_id;
                $args['map_id'] = $map_id;
            } else {
                $post_data['post_parent']   = $args['map_id'];
            }

            $l_id = wp_insert_post($post_data);
            if ( ! $l_id || is_wp_error( $l_id ) ) {
                return false;
            }
        }


        $post_meta = array(
            'address',
            'latitude',
            'longitude',
            'state',
            'postal_code',
            'city',
            'country',
            'redirect_url',
            'marker',
            'map_id',
        );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            foreach ( $post_meta as $meta_key ) {
                $val = isset( $args[ $meta_key ] ) ? $args[ $meta_key ] : '';
                update_post_meta( $post_id, '_location_'.$meta_key, $val );
            }
        } else {
            $post_id = false;
        }

        return $post_id;
    }

    public function update( $args = array(), $map_id = '' ){
        return self::insert( $args, $map_id );
    }


    function get_location( $p ){
        $p = get_post( $p );
        if ( ! $p ) {
            return false;
        }

        $data = array();
        $meta_fields = $this->get_meta_fields();
        foreach ( $meta_fields as $k => $v ){
            $data[ $k ] = get_post_meta( $p->ID, '_location_'.$k, true );
        }
        $data['title'] = $p->post_title;
        $data['location_id'] = $p->ID;
        $data['info'] = $p->post_content;
        return $data;


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

    function get_meta_fields() {
        $fields = array();
        foreach ( $this->get_meta_settings() as $group ) {
            $group = wp_parse_args( $group, array(
                'id' => ''
            ) );
            if ( $group['id'] == 'locations' ) {

            } else if ( isset( $group['settings']) && is_array( $group['settings'] ) ) {
                foreach ( $group['settings'] as $setting ) {
                    $fields[ $setting['id'] ] = isset( $setting['default'] ) ? $setting['default']: null;
                }

            } else {
                $fields[ $group['id'] ] = isset( $group['default'] ) ? $group['default']: null;
            }
        }
        return $fields;
    }

    function get_meta_settings(){
        $location_settings = array(
            array(
                'title' => esc_html__( 'Location Title', 'mapify' ),
                'type' => 'text',
                'id' => 'title'
            ),
            array(
                'title' => esc_html__( 'Location Address', 'mapify' ),
                'type' => 'text',
                'id' => 'address'
            ),
            array(
                'title' => esc_html__( 'Latitude', 'mapify' ),
                'type' => 'text',
                'id' => 'latitude'
            ),
            array(
                'title' => esc_html__( 'Longitude', 'mapify' ),
                'type' => 'text',
                'id' => 'longitude'
            ),

            array(
                'title' => esc_html__( 'City', 'mapify' ),
                'type' => 'text',
                'id' => 'city'
            ),

            array(
                'title' => esc_html__( 'State', 'mapify' ),
                'type' => 'text',
                'id' => 'state'
            ),
            array(
                'title' => esc_html__( 'Postal Code', 'mapify' ),
                'type' => 'text',
                'id' => 'postal_code'
            ),
            array(
                'title' => esc_html__( 'Country', 'mapify' ),
                'type' => 'text',
                'id' => 'country'
            ),
            array(
                'title' => esc_html__( 'Infowindow', 'mapify' ),
                'type' => 'textarea',
                'id' => 'infowindow'
            ),


        );
        return apply_filters( 'mapify_location_settings', $location_settings );
    }

}

function Mapify_Location(){
    if ( ! isset( $GLOBALS['Mapify_Location'] ) ) {
        $GLOBALS['Mapify_Location'] = new Mapify_Location();
    }
    return $GLOBALS['Mapify_Location'] ;
}