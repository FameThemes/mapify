<div class="mapify-maps">
    <?php
    // Maps append here by JS.
    ?>
</div>

<script type="text/html" id="mapify-loop-map-tpl">
    <div class="mapify-map-item" data-map-id="{{ data.map_id }}">
        <div class="gmap-preview-wrapper"><div class="gmap-preview"></div></div>
        <div class="map-meta">
            <div class="map-name">{{ data.map_title }}</div>
            <a class="del-map" data-map-id="{{ data.map_id }}" href="#"><span class="dashicons dashicons-trash"></span></a>
        </div>
    </div>
</script>