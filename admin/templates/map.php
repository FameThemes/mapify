<div id="js-debug"></div>
<script type="text/html" id="mapify-infowindow-template">
    <div class="infowindow gmap-infowindow">
        <# if ( data.title ) { #>
            <div class="info-field">
                <strong class="value">{{ data.title }}</strong>
            </div>
        <# } #>
        <# if (  data.latitude || data.longitude ) { #>
            <div class="info-field half">
                <div class="label"><?php esc_html_e( 'Latitude', 'mapify' ) ?></div>
                <div class="value">{{ data.latitude }}</div>
            </div>
        <# } #>

        <# if ( data.longitude ) { #>
            <div class="info-field half">
                <div class="label"><?php esc_html_e( 'Longitude', 'mapify' ) ?></div>
                <div class="value">{{ data.longitude }}</div>
            </div>
        <# } #>

        <# if (  data.postal_code ) { #>
            <div class="info-field half">
                <div class="label"><?php esc_html_e( 'Postal Code', 'mapify' ) ?></div>
                <div class="value">{{ data.postal_code }}</div>
            </div>
        <# } #>

        <# if (  data.city ) { #>
            <div class="info-field half">
                <div class="label"><?php esc_html_e( 'City', 'mapify' ) ?></div>
                <div class="value">{{ data.city }}</div>
            </div>
        <# } #>

        <# if (  data.state ) { #>
            <div class="info-field half">
                <div class="label"><?php esc_html_e( 'State', 'mapify' ) ?></div>
                <div class="value">{{ data.state }}</div>
            </div>
        <# } #>

        <# if (  data.country ) { #>
            <div class="info-field half">
                <div class="label"><?php esc_html_e( 'Country', 'mapify' ) ?></div>
                <div class="value">{{ data.country }}</div>
            </div>
        <# } #>

        <a href="#" data-l-id="{{ data.location_id }}" class="del-location"><?php esc_html_e( 'Delete', 'mapify' ); ?></a>
    </div>
</script>
<script type="text/html" id="mapify-location-li">
    <li class="location-item" data-id="{{ data.location_id }}">
        <div class="map-og-heading"><# if ( data.title ) {  #>{{ data.title }}<# } else { #><?php esc_html_e( 'Untitled', 'mapify' ); ?><# }  #></div>
        <div class="map-og-settings">
            <form class="mapify-location-form" data-l-id="location-id-{{ data.location_id }}">
            <?php
            echo Mapify_Meta()->render( Mapify_Location()->get_meta_settings() );
            ?>
            </form>
            <a href="#" data-l-id="{{ data.location_id }}" class="del-location"><?php esc_html_e( 'Delete', 'mapify' ); ?></a>
        </div>
    </li>
</script>

<script type="text/html" id="mapify-locations-template">
    <div class="locations-sidebar image-details">
        <h2>
            <div class="location-actions">
                <a class="new-marker first-menu" data-action="new-marker" href="#"><?php esc_html_e( 'Add Location', 'mapify' ); ?></a>
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
                                    <?php
                                    echo Mapify_Meta()->render( Mapify_Map()->get_meta_settings() );
                                    ?>
                                </form>
                            </div>
                            <form class="compat-item"></form>
                        </div>
                        <div class="attachments ">
                            <div class="map-preview"></div>
                        </div>
                    </div>
                </div>
                <div class="media-frame-toolbar">
                    <div class="media-toolbar">
                        <div class="action-msg"></div>
                        <div class="media-toolbar-secondary"></div>
                        <div class="media-toolbar-primary search-form">

                            <button type="button" disabled="disabled" class="mapify-save button media-button button-primary button-large media-button-select"><?php esc_html_e( 'Save Changes', 'mapify' ); ?></button>
                            <span class="spinner"></span>
                            <button type="button" class="mapify-center button media-button button-secondary button-large"><?php esc_html_e( 'Set Center', 'mapify' ); ?></button>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


</script>

