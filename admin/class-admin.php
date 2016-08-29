<?php
class Mapify_Admin {
    public $page_url;
    public $api_key;
    function __construct()
    {
        $this->page_url = add_query_arg( array( 'page' => 'mapify' ), admin_url( 'upload.php' ) ) ;
        $this->api_key = get_option( 'mapify_google_map_api_key' );

        add_action('admin_menu', array( $this, 'add_menu' ) );
        add_action('admin_enqueue_scripts', array( $this, 'scripts' ) );
        add_action( 'wp_ajax_mapify_save', array( $this, 'ajax_save' ) );
        add_action( 'wp_ajax_mapify_load_map', array( $this, 'ajax_load_map' ) );
        add_action( 'wp_ajax_mapify_del_map', array( $this, 'ajax_del_map' ) );
        add_action( 'wp_ajax_mapify_del_location', array( $this, 'ajax_del_location' ) );

        add_action( 'wp_ajax_mapify_load_maps', array( $this, 'ajax_load_maps' ) );

    }

    function ajax_load_maps(){
        $maps = get_posts( array(
            'posts_per_page'   => -1,
            'post_type'   => 'map',
        ) );
        $ajax_maps = array();
        foreach ( $maps as $m ) {
            $ajax_maps[$m->ID] = Mapify_Map()->get_data($m->ID);
        }
        wp_send_json_success( $ajax_maps );
    }

    function ajax_del_map(){
        $nonce = isset( $_POST['_nonce'] ) ?  $_POST['_nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'mapify_nonce_action' ) ) {
            wp_die('security_check');
        }
        Mapify_Map()->delete( absint( $_POST['map_id'] ) );
        wp_send_json_success( );
        die();
    }

    function ajax_del_location(){
        $nonce = isset( $_POST['_nonce'] ) ?  $_POST['_nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'mapify_nonce_action' ) ) {
            wp_die('security_check');
        }
        Mapify_Location()->delete( absint( $_POST['location_id'] ) );
        wp_send_json_success( );
        die();
    }

    function ajax_load_map(){
        $nonce = isset( $_POST['_nonce'] ) ?  $_POST['_nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'mapify_nonce_action' ) ) {
            wp_die('security_check');
        }
        $map_id = $_POST['map_id'];
        $map_data = Mapify_Map()->get_data( $map_id );
        $locations = Mapify_Location()->get_locations(array( 'map_id' => $map_id ) );
        if ( count( $locations ) ) {
            if (!$map_data['center_latitude'] && !$map_data['center_latitude']) {
                $first_location = current($locations);
                $map_data['center_latitude'] = $first_location['latitude'];
                $map_data['center_longitude'] = $first_location['longitude'];
                reset($locations);
            }
        }
        wp_send_json_success( array(
            'map' => $map_data,
            'locations' => $locations
        ) );
        die();
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

        $is_new = false;

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
                $is_new = true;
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
        foreach ( $map_settings as $k => $v ) {
            if ( isset( $data['map'][ $k ] ) ) {
                update_post_meta( $map_id, '_map_'.$k, $data['map'][ $k ] );
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

        $r_data =  array(
            'map_id' => $map_id,
            'locations' => $location_ids,
            'is_new' => $is_new
        );
        if ( $is_new  && $map_id ) {
            $r_data['map_data'] = Mapify_Map()->get_data( $map_id );
        }

        wp_send_json_success( $r_data );
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
            'saving' => esc_html__( 'Saving...', 'mapify' ),
            'save_changes' => esc_html__( 'Save Changes', 'mapify' ),
            'helps' => array(
                'new_marker' => esc_html__( 'Right click on the map to add marker.', 'mapify' ),
                'new_polygon' => esc_html__( 'Right click on the map to add marker, Click Done button to complete.', 'mapify' ),
                'new_circle' => esc_html__( 'Right click on the map to add circle.', 'mapify' ),
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

    function save_settings(){
        if ( isset( $_POST['submit'] ) ) {
            $nonce = isset($_POST['_nonce']) ? $_POST['_nonce'] : '';
            if (!wp_verify_nonce($nonce, 'mapify_nonce_action')) {
                wp_die('security_check');
            }

            if (isset($_POST['mapify_api_key'])) {
                update_option('mapify_google_map_api_key', sanitize_text_field($_POST['mapify_api_key']));
                $this->api_key = get_option( 'mapify_google_map_api_key' );
            }
        }
    }
    function display_settings()
    {
        $current_tab = Mapify_Admin()->get_current_tab();
        ?>
        <form method="post" class="mapify-form-settings" action="<?php echo esc_url( add_query_arg( array( 'tab' => $current_tab ), $this->page_url ) ); ?>">
            <?php wp_nonce_field('mapify_nonce_action', '_nonce' ); ?>
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row"><label for="api_key"><?php esc_html_e( 'Google Map API Key', 'mapify' ); ?></label></th>
                    <td>
                        <input type="text" class="regular-text" value="<?php echo esc_attr( $this->api_key ); ?>" id="api_key" name="mapify_api_key">
                        <p class="description"><?php printf( esc_html__( 'Get Google map API key %1$s', 'mapify' ), '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">'.esc_html__('Here', 'mapify').'</a>' ); ?></p>
                    </td>
                </tr>
                <?php do_action( 'mapify_settings_more_fields' ); ?>
                </tbody>
            </table>
            <p class="submit"><input type="submit" value="<?php esc_attr_e( 'Save Changes' ,'mapify' ); ?>" class="button button-primary" id="submit" name="submit"></p>
        </form>
        <?php
    }

    function get_tabs(){
        $tabs = array(
            'maps' => array(
                'title' => esc_html__( 'Maps', 'mapify' ),
                'callback' => array( $this, 'display_maps' )
            ),
            'settings' => array(
                'title' => esc_html__( 'Settings', 'mapify' ),
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
        if ( isset( $_POST['submit'] ) ) {
            $this->save_settings();
        }
        ?>
        <div class="wrap">
            <h1>
                <?php esc_html_e( 'Mapify', 'mapify' ); ?>
                <?php if ( $this->api_key ) { ?>
                <a class="page-title-action mapify-new" href="#"> <?php esc_html_e( 'Add New', 'mapify' ); ?></a>
                <?php } ?>
            </h1>
            <?php
            // Maybe save settings
            if ( isset( $_POST['submit'] ) ) {
                ?>
                <div class="updated settings-error notice is-dismissible" id="setting-error-settings_updated">
                    <p><strong><?php esc_html_e( 'Settings saved.', 'mapify' ); ?></strong></p><button class="notice-dismiss" type="button"></button>
                </div><?php
            }

            if ( $this->api_key ) {
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
            <?php } else {
                ?>
                <div class="error settings-error notice" id="setting-error-settings_warning">
                    <p><strong><?php esc_html_e( 'You must enter google map API to continue.', 'mapify' ); ?></strong></p>
                </div>
                <?php
                add_action( 'mapify_admin_tab_content', $tabs[ 'settings' ]['callback'] );
                do_action( 'mapify_admin_tab_content' );
            } ?>

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
