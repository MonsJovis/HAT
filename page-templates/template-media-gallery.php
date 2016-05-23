<?php
/*
  Template Name: Gallery
*/

/*
Theme Name: 	MPAT
Theme URI: 		http://mpat.fokus.fraunhofer.de/wordpress/
Description: 	Multi-platform Application Toolkit
Version: 		0.1
Author: 		Fraunhofer Fokus
Author URI: 	http://www.fokus.fraunhofer.de/go/fame
Tags: 			hbbtv
*/

Mpat_Utilities::get_template_parts( array( 'frontend/parts/html-header', 'frontend/parts/header' ) );
$postMeta = get_post_meta( $post->ID, '_mpat_pageContent', true);
if(!$postMeta) { $postMeta = array(); }
if (isset($postMeta['list_position']) && !empty($postMeta['list_position'])){
	$list_pos = $postMeta['list_position'];
} else {
	$list_pos = 'left';
}
if (isset($postMeta['cover_orientation']) && !empty($postMeta['cover_orientation'])){
	$orientation = $postMeta['cover_orientation'];
} else {
	$orientation = 'portrait';
}
?>
<div id="content" class="<?php echo $orientation.' '.$list_pos; ?>">
	<script>
		var theme_url = '<?php echo get_template_directory_uri(); ?>';
	</script>
	<div id="gallery-item-list">
		<div class="scroll-container">
			<div class="spacing-dummy"></div>
			<?php
			$tags = wp_get_post_tags($post->ID, array( 'fields' => 'ids' ));
			$args = array(
				'post_type'        => 'mpat_gallery_item',
				'post_status'      => 'publish',
				'tag__in' => $tags,
				);
			$posts = get_posts( $args ); 
			foreach ( $posts as $post ) {
				$meta = get_post_meta($post->ID,'_mpat_galleryItemContent',true);
				$cover_url = $meta['cover_url'];
				?>

				<div class="gallery-item navigable" item-id="<?php echo $post->ID; ?>">
					<div class="cover" style="background-image: url(<?php echo $cover_url; ?>)"></div>	
				</div>
				<?php
			}
			?>
			<div class="spacing-dummy"></div>
		</div>
	</div>
	<div id="item-info">

		<div id="description-wrapper" style="display: none">
			<div class="contentHeader" id="item-title"></div>
			<div class="textContent" id="description"></div>
		</div>
		<div id="item-info-trailer" class=style=""></div>
	</div>
</div>
<?php
Mpat_Utilities::get_template_parts( array( 'frontend/parts/footer','frontend/parts/html-footer') );
?>