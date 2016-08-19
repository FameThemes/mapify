<div class="mapify-maps">
    <?php
    $maps = get_posts( array(
        'posts_per_page'   => -1,
        'post_type'   => 'map',
    ) );

    foreach ( $maps as $m ) {

        $data = Mapify_Map()->get_data( $m->ID );
        ?>
       <div class="map-col">
           <div class="mapify-map-item" data-map-id="<?php echo esc_attr( $m->ID ); ?>" data-map="<?php echo esc_attr( json_encode( $data ) ); ?>">
               <div class="gmap-preview-wrapper"><div class="gmap-preview"></div></div>
               <div class="map-meta">
                   <div class="map-name"><?php echo esc_html( $m->post_title ); ?></div>
               </div>
           </div>
       </div>
        <?php
    }

    ?>
</div>