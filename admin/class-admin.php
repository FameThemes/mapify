<?php
class Mapify_Admin {
    public $page_url;
    function __construct()
    {
        $this->page_url = add_query_arg( array( 'page' => 'mapify' ), admin_url( 'upload.php' ) ) ;
        add_action('admin_menu', array( $this, 'add_menu' ) );
        add_action('admin_enqueue_scripts', array( $this, 'scripts' ) );

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
            'nonce' => wp_create_nonce( 'gmap_nonce_action' ),
            'confirm' => esc_html__( 'Are your sure ?', 'mapify' ),
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
        echo 'máps';
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
        include dirname( __FILE__ ).'/templates/map.php';
        ?>
        <div class="wrap">
            <h1>
                <?php esc_html_e( 'Mapify', 'mapify' ); ?>
                <a class="page-title-action" href="#"> <?php esc_html_e( 'Add New', 'mapify' ); ?></a>
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
