<?php 

/*
Theme Name: 	MPAT
Theme URI: 		http://mpat.fokus.fraunhofer.de/wordpress/
Description: 	Multi-platform Application Toolkit
Version: 		0.1
Author: 		Fraunhofer Fokus
Author URI: 	http://www.fokus.fraunhofer.de/go/fame
Tags: 			hbbtv
*/
Mpat_Utilities::get_template_parts( array( 'frontend/parts/html-header', 'frontend/parts/header' ) ); ?>

	<div class="content">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<div id="content_oneColum"><?php the_content(); ?></div>
		<?php endwhile; ?>
	</div>

<?php Mpat_Utilities::get_template_parts( array( 'frontend/parts/footer','frontend/parts/html-footer') ); ?>