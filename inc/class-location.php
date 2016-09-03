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

       // print_r( $args );

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
                unset( $args['title'] );
            }
            if ( isset( $args['info'] ) ) {
                $post_data['post_content'] = $args['info'];
                unset( $args['info'] );
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

            $post_id = wp_insert_post($post_data);
            if ( ! $post_id || is_wp_error( $post_id ) ) {
                return false;
            }
        }

        $location_fields = $this->get_meta_fields();

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            foreach ( $location_fields as $k => $v ) {
                if ( isset( $args[ $k ] ) ) {
                    update_post_meta( $post_id, '_location_'.$k, $args[ $k ] );
                } else {
                }
            }
        } else {
            $post_id = false;
        }

        return $post_id;
    }

    public function update( $args = array(), $map_id = '' ){
        return self::insert( $args, $map_id );
    }

    public function delete( $location_id ){
        return wp_delete_post( $location_id, true );
    }


    function get_location( $p ){
        $p = get_post( $p );
        if ( ! $p ) {
            return false;
        }

        $data = array();
        $meta_fields = $this->get_meta_fields( true );
        $media_fields = array();
        foreach ( $meta_fields as $k => $option ){
            $data[ $k ] = get_post_meta( $p->ID, '_location_'.$k, true );
            if ( in_array( $option['type'], array( 'marker', 'media' ) ) ) {
                $media_fields[ $k ] = $option;
            }
        }

        foreach ( $media_fields as $k => $option ) {
            if ( isset( $data[ $k. '__type' ] ) && $data[ $k. '_id' ] ) {
                if ( $data[ $k. '__type' ] == 'video' ) {
                    $data[ $k ] = wp_get_attachment_url( $data[ $k. '__id' ] );
                } else {
                    $size = isset( $option['size'] ) ? $option['size'] : 'thumbnail';
                    $image_attributes = wp_get_attachment_image_src( $data[ $k. '__id'], $size  );
                    if ( $image_attributes ) {
                        $data[ $k ] = $image_attributes[0];
                        $data[ $k.'__width' ] = $image_attributes[1];
                        $data[ $k.'__height' ] = $image_attributes[2];
                    }
                }
            }
        }

        $data['title'] = $p->post_title;
        $data['location_id'] = $p->ID;
        $data['info'] = $p->post_content;
        $data['map_id'] = $p->post_parent;
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

    function get_meta_fields( $get_option = false ) {
        return Mapify_Meta()->get_settings_fields_array( $this->get_meta_settings(), $get_option );
    }

    static function info_tpl(){
    ?>
    <script type="text/html" id="mapify-infowindow-template">
        <div class="infowindow mapify-infowindow">
            <# if ( data.title ) { #>
                <div class="info-field">
                <strong class="value">{{ data.title }}</strong>
                </div>
            <# } #>

            <# if ( data.address ) { #>
                <div class="info-field half">
                    <div class="label"><?php esc_html_e( 'Address', 'mapify' ) ?></div>
                    <div class="value">{{ data.address }}</div>
                </div>
            <# } #>

            <# if (  data.postal_code ) { #>
                <div class="info-field half">
                <div class="label"><?php esc_html_e( 'Postal Code', 'mapify' ) ?></div>
                <div class="value">{{ data.postal_code }}</div>
                </div>
            <# } #>

            <# if (  data.city ) { #>
                <div class="info-field half">
                <div class="label"><?php esc_html_e( 'City', 'mapify' ) ?></div>
                <div class="value">{{ data.city }}</div>
                </div>
            <# } #>

            <# if (  data.state ) { #>
                <div class="info-field half">
                <div class="label"><?php esc_html_e( 'State', 'mapify' ) ?></div>
                <div class="value">{{ data.state }}</div>
                </div>
            <# } #>

            <# if ( data.country ) { #>
                <div class="info-field half">
                <div class="label"><?php esc_html_e( 'Country', 'mapify' ) ?></div>
                <div class="value">{{ data.country }}</div>
                </div>
            <# } #>

            <# if (  data.infowindow ) { #>
                <div class="info-field field-infowindow">
                    <div class="value">{{ data.infowindow }}</div>
                </div>
            <# } #>

        </div>
    </script>
    <?php

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
            array(
                'title' => esc_html__( 'Marker', 'mapify' ),
                'type'  => 'media',
                'id'    => 'marker',
                'size'  => 'mapify-marker'
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