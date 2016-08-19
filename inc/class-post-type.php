<?php

class Mapify_Post_Type {

    function __construct()
    {
        add_action( 'init', array( $this, 'register' ) );
    }

    /**
     * Register post types.
     *
     */
    function register() {
        // Map post type
        $map_labels = array(
            'name'               => _x( 'Maps', 'post type general name', 'mapify' ),
            'singular_name'      => _x( 'Map', 'post type singular name', 'mapify' ),
            'menu_name'          => _x( 'Maps', 'admin menu', 'mapify' ),
            'name_admin_bar'     => _x( 'Map', 'add new on admin bar', 'mapify' ),
            'add_new'            => _x( 'Add New Map', 'map', 'mapify' ),
            'add_new_item'       => __( 'Add New Map', 'mapify' ),
            'new_item'           => __( 'New Map', 'mapify' ),
            'edit_item'          => __( 'Edit Map', 'mapify' ),
            'view_item'          => __( 'View Map', 'mapify' ),
            'all_items'          => __( 'All Maps', 'mapify' ),
            'search_items'       => __( 'Search Maps', 'mapify' ),
            'parent_item_colon'  => __( 'Parent Maps:', 'mapify' ),
            'not_found'          => __( 'No Maps found.', 'mapify' ),
            'not_found_in_trash' => __( 'No Maps found in Trash.', 'mapify' )
        );


        $map_args = array(
            'labels'             => $map_labels,
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'rewrite'            => array( 'slug' => 'map' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-location-alt',
            'supports'           => array( 'title', 'thumbnail', )
        );

        register_post_type( 'map', $map_args );

        // Location post type
        $location_labels = array(
            'name'               => _x( 'Locations', 'post type general name', 'mapify' ),
            'singular_name'      => _x( 'Location', 'post type singular name', 'mapify' ),
            'menu_name'          => _x( 'Locations', 'admin menu', 'mapify' ),
            'name_admin_bar'     => _x( 'Location', 'add new on admin bar', 'mapify' ),
            'add_new'            => _x( 'Add New Location', 'location', 'mapify' ),
            'add_new_item'       => esc_html__( 'Add New Location', 'mapify' ),
            'new_item'           => esc_html__( 'New Location', 'mapify' ),
            'edit_item'          => esc_html__( 'Edit Location', 'mapify' ),
            'view_item'          => esc_html__( 'View Location', 'mapify' ),
            'all_items'          => esc_html__( 'All Locations', 'mapify' ),
            'search_items'       => esc_html__( 'Search Locations', 'mapify' ),
            'parent_item_colon'  => esc_html__( 'Parent Locations:', 'mapify' ),
            'not_found'          => esc_html__( 'No locations found.', 'mapify' ),
            'not_found_in_trash' => esc_html__( 'No locations found in Trash.', 'mapify' )
        );

        $location_args = array(
            'labels'             => $location_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'rewrite'            => array( 'slug' => 'location' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => '',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'author', 'excerpt', )
        );

        register_post_type( 'location', $location_args );

        /**
         * Location category
         * Add new taxonomy, make it hierarchical (like categories)
         */
        $labels = array(
            'name'              => _x( 'Location Categories', 'taxonomy general name', 'mapify' ),
            'singular_name'     => _x( 'Location Category', 'taxonomy singular name', 'mapify' ),
            'search_items'      => esc_html__( 'Search Location Categories', 'mapify' ),
            'all_items'         => esc_html__( 'All Location Categories', 'mapify' ),
            'parent_item'       => esc_html__( 'Parent Location Category', 'mapify' ),
            'parent_item_colon' => esc_html__( 'Parent Location Category:', 'mapify' ),
            'edit_item'         => esc_html__( 'Edit Location Category', 'mapify' ),
            'update_item'       => esc_html__( 'Update Category', 'mapify' ),
            'add_new_item'      => esc_html__( 'Add New Location Category', 'mapify' ),
            'new_item_name'     => esc_html__( 'New Location Category Name', 'mapify' ),
            'menu_name'         => esc_html__( 'Categories', 'mapify' ),
        );

        $args = array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_ui'           => false,
            'show_admin_column' => false,
            'query_var'         => false,
            'show_in_menu'      => false,
            'show_in_nav_menus' => false,
            'show_in_quick_edit' => false,
            'rewrite'           => array( 'slug' => 'location-category' ),
        );
        register_taxonomy( 'location_category', array( 'location' ), $args );

    }

}

new Mapify_Post_Type();