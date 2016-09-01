<?php
class Mapify_Meta {

    function get_settings_fields_array( $groups, $get_option = false ) {
        $fields = array();

        $media_types = array( 'media', 'marker' );

        foreach ( $groups as $group ) {
            $group = wp_parse_args( $group, array(
                'id' => '',
            ) );
            if ( $group['id'] == 'locations' ) {

            } else if ( isset( $group['settings']) && is_array( $group['settings'] ) ) {
                foreach ( $group['settings'] as $setting ) {
                    if ( isset( $setting['type'] ) && in_array( $setting['type'], $media_types ) ) {
                        $arr = $this->get_media_meta_field( $setting, $get_option );
                        foreach( $arr as $k => $v ){
                            $fields[ $k ] = $v;
                        }
                    } else {
                        if ( $get_option ) {
                            $fields[ $setting['id'] ] = $setting;
                        } else {
                            $fields[ $setting['id'] ] = isset( $setting['default'] ) ? $setting['default']: null;
                        }

                    }
                }
            } else {
                if ( isset( $group['type'] ) && in_array( $group['type'], $media_types ) ) {
                    $arr = $this->get_media_meta_field( $group, $get_option );
                    foreach( $arr as $k => $v ){
                        $fields[ $k ] = $v;
                    }
                } else {
                    if ( $get_option ) {
                        $fields[ $group['id'] ] = $group;
                    } else {
                        $fields[ $group['id'] ] = isset( $group['default'] ) ? $group['default']: null;
                    }

                }

            }
        }
        return $fields;
    }

    function get_media_meta_field( $option, $get_option = false ){
        if ( !isset( $option['default'] ) ) {
            $option['default'] = array();
        }

        $option['default'] = wp_parse_args( $option['default'], array(
            'url' => '',
            'id' => '',
            'type' => '',
            'size' => '',
        ) );

        $array = array();
        if ( $get_option ) {
            $array[ $option['id'] ]         = $option;
            $option['child_of']             = $option['id'];
            $option['type']                 = $option['child_media'];
            $array[ $option['id'].'_id' ]   = $option;
            $array[ $option['id'].'_type' ] = $option;
            $array[ $option['id'].'_size' ] = $option;
        } else {
            $array[ $option['id'] ]         = $option['default']['url'];
            $array[ $option['id'].'_id' ]   = $option['default']['id'];
            $array[ $option['id'].'_type' ] = $option['default']['type'];
            $array[ $option['id'].'_size' ] = $option['default']['size'];
        }

        return $array;
    }

    function field( $setting ){
        $html = '';
        $setting = wp_parse_args( $setting, array(
            'title' => '',
            'type'  => '',
            'id'    => '',
            'help'  => '',
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
                if ( $setting['help'] ) {
                    $html .= '<span class="dashicons dashicons-editor-help"></span>';
                }
                $html .= '<select type="text" name="'.esc_attr( $setting['id'] ).'">';
                foreach ( ( array ) $setting['options'] as $k => $label ) {
                    $html .= '<option <# if ( '.$js_val_name.' == "'.esc_attr( $k ).'" ) { #> selected="selected" <# } #>  value="'.esc_attr( $k ).'">'.$label.'</option>';
                }
                $html .= '</select>';
                $html .= '</label>';
                if ( $setting['help'] ) {
                    $html .= '<div class="help-tooltip">'.$setting['help'].'</div>';
                }
                $html .= '</div>';
                break;
            case 'checkbox' :
                $html .= '<div class="field-input field-'.esc_attr( $setting['type'] ).'">';
                $html .= '<label class="label">';
                $html .= '<input type="checkbox" <# if ( '.$js_val_name.' ) { #> checked="checked" <# } #> value="1" name="'.esc_attr( $setting['id'] ).'" value="'.$js_value.'">';
                $html .= $setting['title'].'</label>';
                if ( $setting['help'] ) {
                    $html .= '<span class="dashicons dashicons-editor-help"></span>';
                    $html .= '<div class="help-tooltip">'.$setting['help'].'</div>';
                }
                $html .= '</div>';
                break;
            case 'textarea' :
                $html .= '<div class="field-input field-'.esc_attr( $setting['type'] ).'">';
                $html .= '<label class="label">'.$setting['title'];
                if ( $setting['help'] ) {
                    $html .= '<span class="dashicons dashicons-editor-help"></span>';
                }
                $html .= '<textarea name="'.esc_attr( $setting['id'] ).'">'.$js_value.'</textarea>';
                $html .= '</label>';
                if ( $setting['help'] ) {
                    $html .= '<div class="help-tooltip">'.$setting['help'].'</div>';
                }
                $html .= '</div>';
                break;
            case 'marker': case 'media':
                $js_val_name = 'data.'.esc_attr( $setting['id'] );
                $js_value = '{{ '.$js_val_name.' }}';

                $html .= '<div class="field-input field-marker">';
                $html .= '<label class="label">'.$setting['title'];
                if ( $setting['help'] ) {
                    $html .= '<span class="dashicons dashicons-editor-help"></span>';
                }
                $html .= '<div class="media-upload <# if ( '.$this->js_name( $setting['id'] ).' ){ #> has-preview<# } #>">';
                    $html .= '<div class="media-preview"><# if ( '.$this->js_name( $setting['id'] ).' ){ #><img src="'.$js_value.'" alt=""/><# } #></div>';
                    $html .= '<a href="#" class="media-remove"></a>';
                    $html .= '<input type="hidden" class="media_url" name="'.esc_attr( $setting['id'] ).'" value="'.$js_value.'">';
                    $html .= '<input type="hidden" class="media_id" name="'.esc_attr( $setting['id'].'_id' ).'" value="'.$this->js_value( $setting['id'].'_id' ).'">';
                    $html .= '<input type="hidden" class="media_type" name="'.esc_attr( $setting['id'].'_type' ).'" value="'.$this->js_value( $setting['id'].'_type' ).'">';
                $html .= '</div>';

                $html .= '</label>';
                if ( $setting['help'] ) {
                    $html .= '<div class="help-tooltip">'.$setting['help'].'</div>';
                }
                $html .= '</div>';
                break;
            default:
                $html .= '<div class="field-input field-text">';
                $html .= '<label class="label">'.$setting['title'];
                if ( $setting['help'] ) {
                    $html .= '<span class="dashicons dashicons-editor-help"></span>';
                }
                $html .= '<input type="text" name="'.esc_attr( $setting['id'] ).'" value="'.$js_value.'">';
                $html .= '</label>';
                if ( $setting['help'] ) {
                    $html .= '<div class="help-tooltip">'.$setting['help'].'</div>';
                }
                $html .= '</div>';
        }

        return $html;
    }

    function js_name( $id ){
        return 'data.'.esc_attr( $id );
    }

    function js_value( $id ){
        return '{{ '.$this->js_name( $id ).' }}';;
    }

    function render( $group_settings ){
        $html = '';
        $html .= '<ul class="map-option-group">';
        foreach ( $group_settings as $group ) {
            $group = wp_parse_args( $group, array(
                'id' => '',
                'desc' => ''
            ) );
            if ( $group['id'] == 'locations' ) {
                $html .= '<li class="group-locations locations">';
                $html .= '<div class="map-og-heading">'.esc_html( $group['group_heading'] ).'</div>';
                $html .= '</li>';
            } else if ( isset( $group['settings']) && is_array(  $group['settings'] ) ) {
                $html .= '<li class="group-'.esc_attr( $group['id'] ).'">';
                $html .= '<div class="map-og-heading">'.esc_html( $group['group_heading'] ).'</div>';
                $html .= '<div class="map-og-settings">';
                if ( $group['desc'] ) {
                    $html .= '<div class="map-og-desc">' . $group['desc'] . '</div>';
                }
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

function Mapify_Meta(){
    if ( ! isset( $GLOBALS['Mapify_Meta'] ) ) {
        $GLOBALS['Mapify_Meta'] = new Mapify_Meta();
    }
    return $GLOBALS['Mapify_Meta'] ;
}