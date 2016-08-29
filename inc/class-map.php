<?php
class Mapify_Map {

    function get_data( $map_id ){
        $post = get_post( $map_id );
        if ( ! $post ) {
            return false;
        }
        $data = array();
        $meta_fields = $this->get_meta_fields();
        foreach ( $meta_fields as $k => $v ){
            $data[ $k ] = get_post_meta( $post->ID, '_map_'.$k, true );
        }
        $data['map_title'] = $post->post_title;
        $data['map_id'] = $post->ID;
        return $data;
    }

    function get_meta_settings(){
        $map_settings = array(

            'locations' => array(
                'group_heading' => esc_html__( 'Locations', 'mapify' ),
                'id' => 'locations',
                'settings' => array(
                    array(
                        'title' => esc_html__( 'Style settings code', 'mapify' ),
                        'type' => 'locations',
                        'id' => 'locations'
                    ),
                )
            ),

            'general' => array(
                'group_heading' => esc_html__( 'General', 'mapify' ),
                'id' => 'general',
                'settings' => array(
                    array(
                        'title' => esc_html__( 'Enable Pan-Controller', 'mapify' ),
                        'type' => 'checkbox',
                        'id' => 'pan_controller'
                    ),
                    array(
                        'title' => esc_html__( 'Enable Zoom-Controller', 'mapify' ),
                        'type' => 'checkbox',
                        'id' => 'zoom_controller'
                    ),
                    array(
                        'title' => esc_html__( 'Enable Map-Type-Controller', 'mapify' ),
                        'type' => 'checkbox',
                        'id' => 'map_type_controller'
                    ),
                    array(
                        'title' => esc_html__( 'Enable Scale-Controller', 'mapify' ),
                        'type' => 'checkbox',
                        'id' => 'scale_controller'
                    ),
                    array(
                        'title' => esc_html__( 'Enable Street-View-Controller', 'mapify' ),
                        'type' => 'checkbox',
                        'id' => 'street_view_controller'
                    ),
                    array(
                        'title' => esc_html__( 'Zoom Level', 'mapify' ),
                        'type' => 'text',
                        'id' => 'zoom_level'
                    ),
                    array(
                        'title' => esc_html__( 'Max Zoom', 'mapify' ),
                        'type' => 'text',
                        'id' => 'zoom_max'
                    ),
                    array(
                        'title' => esc_html__( 'Min Zoom', 'mapify' ),
                        'type' => 'text',
                        'id' => 'zoom_min'
                    ),
                    array(
                        'title' => esc_html__( 'Center Latitude', 'mapify' ),
                        'type' => 'text',
                        'id' => 'center_latitude'
                    ),
                    array(
                        'title' => esc_html__( 'Center Longitude', 'mapify' ),
                        'type' => 'text',
                        'id' => 'center_longitude'
                    ),
                    array(
                        'title' => esc_html__( 'Map Width', 'mapify' ),
                        'type' => 'text',
                        'id' => 'map_width',
                        'help' => esc_html__( 'Map width, Can use % or px example: 100%, 500px,...', 'mapify' ),
                    ),
                    array(
                        'title' => esc_html__( 'Map Height', 'mapify' ),
                        'type' => 'text',
                        'id' => 'map_height',
                        'help' => esc_html__( 'Map height, Can use % or px example: 100%, 500px,...', 'mapify' ),
                    ),
                    array(
                        'title' => esc_html__( 'Map Type', 'mapify' ),
                        'type' => 'select',
                        'id' => 'map_type',
                        'default' => 'ROADMAP',
                        'options' => array(
                            'ROADMAP'   => esc_html__( 'ROADMAP', 'mapify' ),
                            'SATELLITE' => esc_html__( 'SATELLITE', 'mapify' ),
                            'HYBRID'    => esc_html__( 'HYBRID', 'mapify' ),
                            'TERRAIN'   => esc_html__( 'TERRAIN', 'mapify' ),
                        )
                    ),
                    array(
                        'title' => esc_html__( 'Map Language', 'mapify' ),
                        'type' => 'select',
                        'id' => 'map_language',
                        'options' => $this->get_countries()
                    ),
                    array(
                        'title' => esc_html__( 'Wheel Scrolling', 'mapify' ),
                        'type' => 'checkbox',
                        'id' => 'wheel_scrolling'
                    ),
                    array(
                        'title' => esc_html__( 'Map Draggable', 'mapify' ),
                        'type' => 'checkbox',
                        'id' => 'map_draggable'
                    ),

                )
            ),

            'layer' => array(
                'group_heading' => esc_html__( 'Layer', 'mapify' ),
                'id' => 'layer',
                'settings' => array(
                    array(
                        'title' => esc_html__( 'Traffic Layer', 'mapify' ),
                        'type' => 'checkbox',
                        'id' => 'traffic_layer'
                    ),
                    array(
                        'title' => esc_html__( 'Transit Layer', 'mapify' ),
                        'type' => 'checkbox',
                        'id' => 'transit_layer'
                    ),
                    array(
                        'title' => esc_html__( 'Bicycling Layer', 'mapify' ),
                        'type' => 'checkbox',
                        'id' => 'bicycling_layer'
                    ),

                ),
            ),

            'style' => array(
                'group_heading' => esc_html__( 'Style', 'mapify' ),
                'id' => 'style',
                'settings' => array(
                    array(
                        'title' => esc_html__( 'Style settings code', 'mapify' ),
                        'type' => 'textarea',
                        'id' => 'style'
                    ),
                )
            ),


        );
        return apply_filters( 'mapify_map_settings', $map_settings );
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

    function get_countries(){
        $countries = array(
            'location based' => esc_attr('Location Based', 'mapify'),
            'ar' => esc_attr('Arabic', 'mapify'),
            'bg' => esc_attr('Bulgarian', 'mapify'),
            'bn' => esc_attr('Bengali', 'mapify'),
            'ca' => esc_attr('Catalan', 'mapify'),
            'cs' => esc_attr('Czech', 'mapify'),
            'da' => esc_attr('Danish', 'mapify'),
            'de' => esc_attr('German', 'mapify'),
            'el' => esc_attr('Greek', 'mapify'),
            'en' => esc_attr('English', 'mapify'),
            'en-AU' => esc_attr('English (Australian)', 'mapify'),
            'en-GB' => esc_attr('English (Great Britain)', 'mapify'),
            'es' => esc_attr('Spanish', 'mapify'),
            'eu' => esc_attr('Basque', 'mapify'),
            'fa' => esc_attr('Farsi', 'mapify'),
            'fi' => esc_attr('Finnish', 'mapify'),
            'fil' => esc_attr('Finnish', 'mapify'),
            'fr' => esc_attr('French', 'mapify'),
            'gl' => esc_attr('Galician', 'mapify'),
            'gu' => esc_attr('Gujarati', 'mapify'),
            'hi' => esc_attr('Hindi', 'mapify'),
            'hr' => esc_attr('Croatian', 'mapify'),
            'hu' => esc_attr('Hungarian', 'mapify'),
            'id' => esc_attr('Indonesian', 'mapify'),
            'it' => esc_attr('Italian', 'mapify'),
            'iw' => esc_attr('Hebrew', 'mapify'),
            'ja' => esc_attr('Japanese', 'mapify'),
            'kn' => esc_attr('Kannada', 'mapify'),
            'ko' => esc_attr('Korean', 'mapify'),
            'lt' => esc_attr('Lithuanian', 'mapify'),
            'lv' => esc_attr('Latvian', 'mapify'),
            'ml' => esc_attr('Malayalam', 'mapify'),
            'mr' => esc_attr('Marathi', 'mapify'),
            'nl' => esc_attr('Dutch', 'mapify'),
            'no' => esc_attr('Norwegian', 'mapify'),
            'pl' => esc_attr('Polish', 'mapify'),
            'pt' => esc_attr('Portuguese', 'mapify'),
            'pt-BR' => esc_attr('Portuguese (Brazil)', 'mapify'),
            'pt-PT' => esc_attr('Portuguese (Portugal)', 'mapify'),
            'ro' => esc_attr('Romanian', 'mapify'),
            'ru' => esc_attr('Russian', 'mapify'),
            'sk' => esc_attr('Slovak', 'mapify'),
            'sl' => esc_attr('Slovenian', 'mapify'),
            'sr' => esc_attr('Serbian', 'mapify'),
            'sv' => esc_attr('Swedish', 'mapify'),
            'ta' => esc_attr('Tamil', 'mapify'),
            'te' => esc_attr('Telugu', 'mapify'),
            'th' => esc_attr('Thai', 'mapify'),
            'tl' => esc_attr('Tagalog', 'mapify'),
            'tr' => esc_attr('Turkish', 'mapify'),
            'uk' => esc_attr('Ukrainian', 'mapify'),
            'vi' => esc_attr('Vietnamese', 'mapify'),
            'zh-CN' => esc_attr('Chinese (Simplified)', 'mapify'),
            'zh-TW' => esc_attr('Chinese (Traditional)', 'mapify'),
        );

        return $countries;
    }

    function delete( $map_id ){
        $map = get_post( $map_id );
        if ( $map ) {
            global $wpdb;
            wp_delete_post( $map->ID );
            $location_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'location'", $map->ID ) );
            // Delete all location of this map
            foreach ( $location_ids as $_id ) {
                wp_delete_post( $_id );
            }
        }
    }
}

function Mapify_Map(){
    if ( ! isset( $GLOBALS['Mapify_Map'] ) ) {
        $GLOBALS['Mapify_Map'] = new Mapify_Map();
    }
    return $GLOBALS['Mapify_Map'] ;
}