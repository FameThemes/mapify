<?php
/*
Plugin Name: Mapify
Plugin URI: https://www.famethemes.com/
Description: A WordPress Map plugin.
Version: 1.0.0
Author: famethemes, shrimp2t
Author URI: https://www.famethemes.com/
Text Domain: mapify
Domain Path: /languages
*/


class Mapify {
    public $url;
    public $path;

    function __construct()
    {
        $this->url  = trailingslashit( plugins_url('', __FILE__) );
        $this->path = trailingslashit( plugin_dir_path( __FILE__) );
        $this->includes();
    }

    function includes()
    {
        require_once $this->path.'inc/class-location.php';
        if ( is_admin() ) {
            require_once $this->path.'admin/class-meta.php';
            require_once $this->path.'admin/class-admin.php';
        }
    }

    function load_gmap_js(){
        wp_enqueue_script( 'google-maps-api', 'https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyASkFdBVeZHxvpMVIOSfk2hGiIzjOzQeFY', null, null );
    }

    function scripts()
    {
        $this->load_gmap_js();
        wp_enqueue_script( 'google-maps', GMAP_URL.'assets/js/map-metabox.js', array( 'jquery', 'google-maps-api', 'json2' ) );
        wp_enqueue_style( 'google-maps', GMAP_URL.'assets/css/metabox.css', array() );
        wp_localize_script( 'google-maps', 'GMAP', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce( 'gmap_nonce_action' ),
            'confirm' => esc_html__( 'Are your sure ?' ),
        ) );
    }

    function init()
    {


    }


}

$GLOBALS['Mapify'] = new Mapify();
function Mapify(){
    if ( ! isset( $GLOBALS['Mapify'] ) ) {
        $GLOBALS['Mapify'] = new Mapify();
    }
    return $GLOBALS['Mapify'];
}



// ==================for debug===============================
if(!function_exists('s_help_screen_help')){
    add_action( 'contextual_help', 's_help_screen_help', 10, 3 );
    function s_help_screen_help( $contextual_help, $screen_id, $screen ) {
        // The add_help_tab function for screen was introduced in WordPress 3.3.
        if ( ! method_exists( $screen, 'add_help_tab' ) )
            return $contextual_help;
        global $hook_suffix;
        // List screen properties
        $variables = '<ul style="width:50%;float:left;"> <strong>Screen variables </strong>'
            . sprintf( '<li> Screen id : %s</li>', $screen_id )
            . sprintf( '<li> Screen base : %s</li>', $screen->base )
            . sprintf( '<li>Parent base : %s</li>', $screen->parent_base )
            . sprintf( '<li> Parent file : %s</li>', $screen->parent_file )
            . sprintf( '<li> Hook suffix : %s</li>', $hook_suffix )
            . '</ul>';
        // Append global $hook_suffix to the hook stems
        $hooks = array(
            "load-$hook_suffix",
            "admin_print_styles-$hook_suffix",
            "admin_print_scripts-$hook_suffix",
            "admin_head-$hook_suffix",
            "admin_footer-$hook_suffix"
        );
        // If add_meta_boxes or add_meta_boxes_{screen_id} is used, list these too
        if ( did_action( 'add_meta_boxes_' . $screen_id ) )
            $hooks[] = 'add_meta_boxes_' . $screen_id;
        if ( did_action( 'add_meta_boxes' ) )
            $hooks[] = 'add_meta_boxes';
        // Get List HTML for the hooks
        $hooks = '<ul style="width:50%;float:left;"> <strong>Hooks </strong> <li>' . implode( '</li><li>', $hooks ) . '</li></ul>';
        // Combine $variables list with $hooks list.
        $help_content = $variables . $hooks;
        // Add help panel
        $screen->add_help_tab( array(
            'id'      => 'wptuts-screen-help',
            'title'   => 'Screen Information',
            'content' => $help_content,
        ));
        return $contextual_help;
    }
}