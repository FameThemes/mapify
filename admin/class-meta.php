<?php
class Mapify_Meta {

    function get_map_fields(){
        return $this->get_settings_fields_array( $this->get_map_settings() );
    }

    function get_locations_fields(){
        return  $this->get_settings_fields_array( $this->get_location_settings() );
    }

    function get_settings_fields_array( $group_settings ) {
        $fields = array();
        foreach ( $group_settings as $group ) {
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

    function get_map_settings(){
        return Mapify_Map()->get_meta_settings();
    }

    function get_location_settings(){
        return Mapify_Location()->get_meta_settings();
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