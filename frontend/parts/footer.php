<?php /*
Theme Name: 	MPAT
Theme URI: 		http://mpat.fokus.fraunhofer.de/wordpress/
Description: 	Multi-platform Application Toolkit
Version: 		0.1
Author: 		Fraunhofer Fokus
Author URI: 	http://www.fokus.fraunhofer.de/go/fame
Tags: 			hbbtv
*/
?>
<footer>
	<?php
	$locations = get_nav_menu_locations();
	if (isset($locations['primary-menu']) && !empty($locations['primary-menu'])){
	?>
	<div id="menu-toggle">
		<?php $btn = get_post_meta(1,'_main_menu_button',true); if(empty($btn)) {$btn = '5';}?>
		<img src="<?php echo get_bloginfo('template_url').'/shared/assets/button'.$btn.'.png'; ?>"></img>
		<div class="menuText"><?php echo get_theme_mod( 'menu_text') ; ?></div>
	</div>
	<script>
		jQuery(document).ready(function($){
			$generalNav.nav('<?php echo $btn; ?>',function(){
					if (!$('#menu-toggle').hasClass('primary-menu')){
						$('#menu-toggle').activate();
					} else {
						$('#menu-toggle').destroy();
						$('.navigable').eq(0).activate();
					}
			});
		});
	</script>
	<?php 
	wp_nav_menu( array('container_id' => 'primary-menu-wrap', 'theme_location' => 'primary-menu', 'walker' => new mpat_walker_primary_menu  ) );
	}

if (isset($locations['footer-menu']) && !empty($locations['footer-menu']) ){
	wp_nav_menu( array( 'theme_location' => 'footer-menu','depth'=>-1,'walker'=> new mpat_walker_footer_menu ) );
} else {
	?>
		<div id="hide-button">
		<img src="<?php echo get_bloginfo('template_url').'/shared/assets/buttonRed.png' ?>"></img>
		<div><?php echo get_theme_mod( 'hide_text'); ?></div>
	</div>
	<?php
}

?>
</footer>