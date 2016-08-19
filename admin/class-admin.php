<?php
class Mapify_Admin {
    public $page_url;
    function __construct()
    {
        $this->page_url = add_query_arg( array( 'page' => 'mapify' ), admin_url( 'upload.php' ) ) ;
        add_action('admin_menu', array( $this, 'add_menu' ) );
        add_action('admin_enqueue_scripts', array( $this, 'scripts' ) );
        add_action( 'wp_ajax_mapify_save', array( $this, 'ajax_save' ) );
        add_action( 'wp_ajax_mapify_load_map', array( $this, 'ajax_load_map' ) );

    }

    function ajax_load_map(){
        die( is_admin() );
    }

    function ajax_save( ) {
        $nonce = isset( $_POST['_nonce'] ) ?  $_POST['_nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'mapify_nonce_action' ) ) {
            wp_die('security_check');
        }

        $data = $_POST;
        if ( ! isset( $data['map'] ) || ! is_array( $data['map']  ) ) {
            wp_send_json_error( esc_html__( 'Invalid data', 'mapify' ) );
        }

        if ( ! is_array( $data['locations'] ) ) {
            $data['locations'] = array();
        }

        $meta = new Mapify_Meta();
        if ( isset( $data['map_id'] ) ) {
            $map_id = absint( $data['map_id'] );
        } else if ( isset( $data['map']['map_id'] ) ){
            $map_id = absint( $data['map']['map_id'] );
        } else {
            $map_id = 0;
        }

        if ( $map_id ) {
            $post_map = get_post($map_id);
            if ( ! $post_map ) {
                $map_id = 0;
            }
        }

        $map_title = isset( $data['map']['map_title'] ) ? $data['map']['map_title'] : esc_html__( 'Untitled', 'mapify' );


        if ( ! $map_id || ! ( $post = get_post( $map_id ) ) ) { // New map
            $post_id = wp_insert_post( array(
                'post_title' => $map_title,
                'post_type' => 'map',
                'post_status' => 'publish',
            ) );
            if ( $post_id && ! is_wp_error( $post_id ) ) {
                $map_id =  $post_id;
            } else {
                wp_send_json_error( esc_html__( 'Can not save map', 'mapify' ) );
            }
        } else {
            $map_id =  $post->ID;
            wp_update_post( array(
                'post_title' => $map_title,
                'post_status' => 'publish',
                'ID' => $map_id,
            ) );
        }

        // Update map meta
        $map_settings = $meta->get_map_fields();
        foreach ( $data['map'] as $k => $v ) {
            if ( isset( $map_settings[ $k ] ) ) {
                update_post_meta( $map_id, '_map_'.$k, $v );
            }
        }

        // $location_fileds = $meta->get_locations_fields();
        $location_ids = array();
        $l = new Mapify_Location();
        foreach ( $data['locations'] as $key => $location ) {
            if ( isset( $location['location_id'] ) ) {
                $l_id = absint( $location['location_id']  );
            } else {
                $l_id = $key;
            }
            if ( false !== strpos( $l_id, 'new' ) ) { // is_add new
                $id =  $l->insert( $location, $map_id );
                if ( $id ) {
                    $location_ids[ $key ] = $id;
                }
            } else {
                $location['location_id'] = $l_id;
                $id = $l->insert( $location, $map_id );
                if ( $id ) {
                    $location_ids[ $key ] = $id;
                }
            }
        }

        wp_send_json_success( array(
            'map_id' => $map_id,
            'locations' => $location_ids
        ) );
        die();


    }



    function add_menu()
    {
        add_media_page( esc_html__('Mapify','mapify'), esc_html__('Mapify','mapify'), 'manage_options', 'mapify', array( $this, 'display' ) );
    }

    function scripts( $hook )
    {
        if ( false === strpos( $hook, 'mapify' ) ) {
            return ;
        }
        Mapify()->load_gmap_js();
        wp_enqueue_media();
        wp_enqueue_script( 'mapify-admin', Mapify()->url.'assets/js/admin.js', array( 'jquery', 'google-maps-api', 'json2' ), false, true );
        wp_enqueue_style( 'mapify-admin', Mapify()->url.'assets/css/admin.css' );
        wp_localize_script( 'mapify-admin', 'mapify_config', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce( 'mapify_nonce_action' ),
            'confirm' => esc_html__( 'Are your sure ?', 'mapify' ),
            'untitled' => esc_html__( 'Untitled', 'mapify' ),
            'helps' => array(
                'new_marker' => esc_html__( 'Right click on the map to add marker.', 'mapify' ),
                'new_polygon' => esc_html__( 'Right click on the map to add marker, Click Done button to complete.', 'mapify' ),
                'new_circle' => esc_html__( 'Right click on the map to add circl.', 'mapify' ),
            )
        ) );
    }

    function get_current_tab()
    {
        $action = '';
        if ( isset( $_GET['tab'] ) ) {
            $action = sanitize_title( $_GET['tab'] );
        }
        if ( ! $action ) {
            $action = 'maps';
        }
        return $action;
    }

    function display_maps()
    {
        include Mapify()->path.'admin/templates/maps.php';
    }
    function display_settings()
    {
        echo 'settings';
    }

    function get_tabs(){
        $tabs = array(
            'maps' => array(
                'title' => esc_html__( 'Maps', 'mapify' ),
                'callback' => array( $this, 'display_maps' )
            ),
            'settings' => array(
                'title' => esc_html__( 'Maps', 'mapify' ),
                'callback' => array( $this, 'display_settings' )
            )
        );

        return apply_filters( 'mapify_admin_tabs', $tabs );
    }


    function display()
    {
        $current_tab = Mapify_Admin()->get_current_tab();
        $tabs =  $this->get_tabs();
        include Mapify()->path.'admin/templates/map.php';
        ?>
        <div class="wrap">
            <h1>
                <?php esc_html_e( 'Mapify', 'mapify' ); ?>
                <a class="page-title-action mapify-new" href="#"> <?php esc_html_e( 'Add New', 'mapify' ); ?></a>
            </h1>
            <?php

            ?>
            <h2 class="nav-tab-wrapper">
                <?php foreach ( $tabs as $id => $tab ){ ?>
                <a class="nav-tab <?php echo ( $current_tab == $id ) ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'tab' => $id ), $this->page_url ) ); ?>"><?php echo $tab['title'] ?></a>
                <?php } ?>
            </h2>

            <div class="mapify-tab-content">
                <?php
                if( isset( $tabs[ $current_tab ] ) ) {
                    add_action( 'mapify_admin_tab_content', $tabs[ $current_tab ]['callback'] );
                    do_action( 'mapify_admin_tab_content' );
                } else {
                    ?>
                    <p class="no-action">
                        <?php esc_html_e( 'Something wrong, please try again later.', '' ) ?>
                    </p>
                    <?php
                }
                ?>
            </div>

        </div>
        <?php
    }

}

$GLOBALS['Mapify_Admin'] = new Mapify_Admin();
function Mapify_Admin(){
    if ( ! isset( $GLOBALS['Mapify_Admin'] ) ) {
        $GLOBALS['Mapify_Admin'] = new Mapify_Admin();
    }
    return $GLOBALS['Mapify_Admin'];
}
