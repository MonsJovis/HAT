<?php /*
Theme Name: 	MPAT
Theme URI: 		http://mpat.fokus.fraunhofer.de/wordpress/
Description: 	Multi-platform Application Toolkit
Version: 		0.1
Author: 		Fraunhofer Fokus
Author URI: 	http://www.fokus.fraunhofer.de/go/fame
Tags: 			hbbtv
 */
//Checks if the user agent is a SmartTV (And not the Firefox HbbTv Plugin)
if(Mpat_Utilities::user_agent_is_smart_tv()){
    header('Content-type: application/vnd.hbbtv.xhtml+xml; charset=utf-8');
}?>
<!DOCTYPE html PUBLIC "-//HbbTV//1.1.1//EN" "http://www.hbbtv.org/dtd/HbbTV-1.1.1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php bloginfo( 'name' ); wp_title( '' ); ?></title>
		<meta charset="<?php bloginfo( 'charset' ); ?>"></meta>
	  	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"></meta>
        <?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<script>
			parentUrl = "<?php if($post->post_parent){ echo get_permalink($post->post_parent); } else {echo '';} ?>";
			if (parentUrl){
				jQuery(document).ready(function($){
					$generalNav.nav('Back',function(){
						window.location.href = parentUrl;
					});
				});
			}
		</script>
		<div id="hbbtv_app">
			<div id="safe_area">
