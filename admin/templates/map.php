<div id="js-debug"></div>

<?php
$meta = new Mapify_Meta();
?>
<script type="text/html" id="mapify-location-template">
    <div class="location-sidebar image-details">
        <form class="mapify-location-form" data-l-id="location-id-{{ data.location_id }}">
            <input type="hidden" name="map_id" value="{{ data.map_id }}">
            <?php
                echo $meta->render( $meta->get_location_settings() );
            ?>
        </form>
    </div>
</script>
<script type="text/html" id="mapify-location-li">
    <li class="location-item" data-id="{{ data.location_id }}">
        <div class="map-og-heading"><# if ( data.title ) {  #>{{ data.title }}<# } else { #><?php esc_html_e( 'Untitled'); ?><# }  #></div>
        <div class="map-og-settings">
           <form class="mapify-location-form" data-l-id="location-id-{{ data.location_id }}">
               <?php
               echo $meta->render( $meta->get_location_settings() );
               ?>
           </form>
        </div>
    </li>
</script>

<script type="text/html" id="mapify-locations-template">
    <div class="locations-sidebar image-details">
        <h2>
            <?php // esc_html_e( 'Locations', 'mapify' ); ?>
            <div class="location-actions">
                <a class="new-marker first-menu" data-action="new-marker" href="#"><?php esc_html_e( 'Add Location', 'mapify' ); ?></a>
                <?php /*
                <div class="sub-menu-actions">
                    <a class="new-marker" data-action="new-marker" href="#"><?php esc_html_e( 'Add Marker', 'mapify' ); ?></a>
                    <a class="new-polygon" data-action="new-polygon" href="#"><?php esc_html_e( 'Add Polygon', 'mapify' ); ?></a>
                    <a class="new-direction" data-action="new-direction" href="#"><?php esc_html_e( 'Add Direction', 'mapify' ); ?></a>
                    <a class="new-circle" data-action="new-circle" href="#"><?php esc_html_e( 'Add Circle', 'mapify' ); ?></a>
                </div>
                */ ?>
            </div>
            <a href="#" class="locations-close"></a>
        </h2>
        <ul class="map-option-group locations-list">
        </ul>
    </div>
</script>

<script type="text/html" id="mapify-map-template">

    <div class="media-modal wp-core-ui mapify-modal">
        <button class="button-link media-modal-close" type="button">
            <span class="media-modal-icon">
                <span class="screen-reader-text"><?php esc_html_e( 'Close media panel', 'mapify' ); ?></span>
            </span>
        </button>
        <div class="media-modal-content">
            <div class="media-frame mode-select wp-core-ui hide-menu hide-router hide-toolbar---">
                <div class="media-frame-title">
                    <h1><span title="<?php esc_attr_e( 'Click to edit', 'mapify' ); ?>" class="map-title" contenteditable="true"><# if ( data.map_title ) { #>{{ data.map_title }}<# } else { #><?php esc_html_e( 'Untitled', 'mapify' ); ?><# } #></span></h1>
                </div>
                <div class="media-frame-content">
                    <div class="attachments-browser">
                        <div class="media-sidebar">
                            <div tabindex="0" data-id="7" class="attachment-details save-ready">
                                <h2>
                                    <?php esc_html_e( 'Map Settings', 'mapify' ); ?>
                                </h2>
                                <form  class="mapify-map-form">
                                    <input type="hidden" name="map_id" value="{{ data.map_id }}">
                                    <?php
                                    echo $meta->render( $meta->get_map_settings() );
                                    ?>
                                </form>


                            </div>
                            <form class="compat-item"></form>
                        </div>
                        <div class="attachments ">
                            <div class="map-preview"></div>
                            <button class="button-primary button-new-action" type="button">Done</button>
                        </div>
                    </div>
                </div>
                <div class="media-frame-toolbar">
                    <div class="media-toolbar">
                        <div class="action-msg"></div>
                        <div class="media-toolbar-secondary"></div>
                        <div class="media-toolbar-primary search-form">
                            <button type="button" disabled="disabled" class="mapify-save button media-button button-primary button-large media-button-select"><?php esc_html_e( 'Save Changes', 'mapify' ); ?></button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


</script>

