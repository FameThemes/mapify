<?php
$meta = new Mapify_Meta();
?>
<script type="text/html" id="mapify-location-template">
    <div class="location-sidebar image-details">
        <h2>
            <?php esc_html_e( 'Location Details', 'mapify' ); ?>
            <a href="#" class="location-close"></a>
        </h2>
        <form class="mapify-location-form" id="location-id-{{ data.location_id }}">
            <input type="hidden" name="map_id" value="{{ data.map_id }}">
            <?php
                echo $meta->render( $meta->get_location_settings() );
            ?>
        </form>
    </div>
</script>

<script type="text/html" id="mapify-map-template">

    <div class="media-modal wp-core-ui mapify-modal">
        <button class="button-link media-modal-close" type="button">
            <span class="media-modal-icon"><span class="screen-reader-text">Close media panel</span></span>
        </button>
        <div class="media-modal-content">
            <div class="media-frame mode-select wp-core-ui hide-menu hide-router hide-toolbar---" id="__wp-uploader-id-3">
                <div class="media-frame-title">
                    <h1>Featured Image<span class="dashicons dashicons-arrow-down"></span></h1>
                </div>
                <div class="media-frame-content" data-columns="7">
                    <div class="attachments-browser">
                        <div class="media-sidebar">
                            <div tabindex="0" data-id="7" class="attachment-details save-ready">
                                <h2>
                                    <?php esc_html_e( 'Map Settings', 'mapify' ); ?>
                                    <span class="settings-save-status">
                                    <span class="spinner"></span>
                                    <span class="saved">Saved.</span>
                                    </span>
                                </h2>
                                <form  class="mapify-map-form">
                                    <input type="hidden" name="map_id" value="{{ data.map_id }}">
                                    <?php
                                    echo $meta->render( $meta->get_map_settings() );
                                    ?>
                                </form>
                                <h2>
                                    <?php esc_html_e( 'Locations', 'mapify' ); ?>
                                    <span class="settings-save-status">
                                    <span class="spinner"></span>
                                    <span class="saved">Saved.</span>
                                    </span>
                                </h2>

                                <ul class="locations">
                                    <li>49 Tan Tthinh Thai Nguyen</li>
                                    <li>49 Tan Tthinh Thai Nguyen</li>
                                    <li>49 Tan Tthinh Thai Nguyen</li>
                                    <li>49 Tan Tthinh Thai Nguyen</li>
                                    <li>49 Tan Tthinh Thai Nguyen</li>
                                </ul>

                                <div class="location-actions">
                                    <a class="new-marker" href="#"><?php esc_html_e( '+ Marker', 'mapify' ); ?></a>
                                    <a class="new-polygon" href="#"><?php esc_html_e( '+ Polygon', 'mapify' ); ?></a>
                                    <a class="new-direction" href="#"><?php esc_html_e( '+ Direction', 'mapify' ); ?></a>
                                    <a class="new-circle" href="#"><?php esc_html_e( '+ Circle', 'mapify' ); ?></a>
                                </div>



                            </div>
                            <form class="compat-item"></form>
                        </div>

                        <div class="uploader-inline hidden">
                            <div class="uploader-inline-content has-upload-message">
                                <h2 class="upload-message">No items found.</h2>
                                <div class="upload-ui">
                                    <h2 class="upload-instructions drop-instructions">Drop files anywhere to upload</h2>
                                    <p class="upload-instructions drop-instructions">or</p>
                                    <a class="browser button button-hero" href="#" style="display: inline; position: relative; z-index: 1;" id="__wp-uploader-id-4">Select Files</a>
                                </div>
                                <div class="upload-inline-status"></div>
                                <div class="post-upload-ui">
                                    <p class="max-upload-size">Maximum upload file size: 32 MB.</p>
                                </div>
                            </div>
                        </div>
                        <div class="attachments map-preview">
                            Map view here
                        </div>
                    </div>
                </div>
                <div class="media-frame-toolbar">
                    <div class="media-toolbar">
                        <div class="media-toolbar-secondary"></div>
                        <div class="media-toolbar-primary search-form">
                            <button type="button" class="button media-button button-primary button-large media-button-select">Save Changes</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


</script>

