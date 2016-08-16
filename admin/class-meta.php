<?php
class Mapify_Meta {
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
    function get_map_settings(){
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
                        'title' => esc_html__( 'Default Zoom', 'mapify' ),
                        'type' => 'text',
                        'id' => 'default_zoom'
                    ),
                    array(
                        'title' => esc_html__( 'Minimum Zoom', 'mapify' ),
                        'type' => 'text',
                        'id' => 'minimum_zoom'
                    ),
                    array(
                        'title' => esc_html__( 'Maximum Zoom', 'mapify' ),
                        'type' => 'text',
                        'id' => 'maximum_zoom'
                    ),

                    array(
                        'title' => esc_html__( 'Center Address', 'mapify' ),
                        'type' => 'text',
                        'id' => 'center_address'
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
                        'id' => 'map_width'
                    ),
                    array(
                        'title' => esc_html__( 'Map Height', 'mapify' ),
                        'type' => 'text',
                        'id' => 'map_height'
                    ),
                    array(
                        'title' => esc_html__( 'Map Type', 'mapify' ),
                        'type' => 'select',
                        'id' => 'map_type',
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

    function get_location_settings(){
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

    function field( $setting ){
        $html = '';
        $setting = wp_parse_args( $setting, array(
            'title' => '',
            'type'  => '',
            'id'    => '',
        ) );
        $js_val_name = 'data.'.esc_attr( $setting['id'] );
        $js_value = '{{ '.$js_val_name.' }}';

        switch ( $setting['type'] ) {
            case 'select' :
                if ( ! isset( $setting['options'] ) ) {
                    $setting['options'] = array();
                }
                $html .= '<div class="field-input field-'.esc_attr( $setting['type'] ).'">';
                $html .= '<label class="label">'.$setting['title'];

                $html .= '<select type="text" name="'.esc_attr( $setting['id'] ).'">';
                foreach ( ( array ) $setting['options'] as $k => $label ) {
                    $html .= '<option <# if ( '.$js_val_name.' == "'.esc_attr( $k ).'" ) { #> checked="checked" <# } #>  value="'.esc_attr( $k ).'">'.$label.'</option>';
                }
                $html .= '</select>';

                $html .= '</label></div>';
                break;
            case 'checkbox' :
                $html .= '<div class="field-input field-'.esc_attr( $setting['type'] ).'">';
                $html .= '<label class="label">';
                $html .= '<input type="checkbox" <# if ( '.$js_val_name.' ) { #> checked="checked" <# } #> value="1" name="'.esc_attr( $setting['id'] ).'" value="'.$js_value.'">';
                $html .= $setting['title'].'</label></div>';
                break;
            case 'textarea' :
                $html .= '<div class="field-input field-'.esc_attr( $setting['type'] ).'">';
                $html .= '<label class="label">'.$setting['title'];
                $html .= '<textarea name="'.esc_attr( $setting['id'] ).'">'.$js_value.'</textarea>';
                $html .= '</label></div>';
                break;
            default:
                $html .= '<div class="field-input field-text">';
                $html .= '<label class="label">'.$setting['title'];
                $html .= '<input type="text" name="'.esc_attr( $setting['id'] ).'" value="'.$js_value.'">';
                $html .= '</label></div>';
        }

        return $html;
    }

    function render( $group_settings ){
        $html = '';
        $html .= '<ul class="map-option-group">';
        foreach ( $group_settings as $group ) {
            $group = wp_parse_args( $group, array(
                'id' => ''
            ) );
            if ( $group['id'] == 'locations' ) {
                $html .= '<li class="group-locations locations">';
                $html .= '<div class="map-og-heading">'.esc_html( $group['group_heading'] ).'</div>';
                $html .= '</li>';
            } else if ( isset( $group['settings']) && is_array(  $group['settings'] ) ) {
                $html .= '<li class="group-'.esc_attr( $group['id'] ).'">';
                $html .= '<div class="map-og-heading">'.esc_html( $group['group_heading'] ).'</div>';
                $html .= '<div class="map-og-settings">';
                foreach ( $group['settings'] as $setting ) {
                    $html .= $this->field( $setting );
                }
                $html .= '</div>';
                $html .= '</li>';

            } else {
                $html .= '<li class="no-group">';
                $html .= $this->field( $group );
                $html .= '</li>';
            }
        }
        $html .= '</ul>';
        return $html;
    }

}