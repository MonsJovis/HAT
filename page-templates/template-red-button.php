<?php
/*
  Template Name: Red Button
*/

/*
Theme Name:     MPAT
Theme URI:      http://mpat.fokus.fraunhofer.de/wordpress/
Description:    Multi-platform Application Toolkit
Version:        0.1
Author:         Fraunhofer Fokus
Author URI:     http://www.fokus.fraunhofer.de/go/fame
Tags:           hbbtv
*/
    $hpc = get_post_meta($post->ID,'_mpat_pageContent',TRUE);
    if(!$hpc) { $hpc = array(); }

    Mpat_Utilities::get_template_parts(array('frontend/parts/html-header'));
?>
    <img id='redButtonIMG' data-frontpage-url="<?php echo $hpc['url']; ?>" data-fadein-time="<?php echo $hpc['fadeinTime']; ?>" data-display-duration="<?php echo $hpc['displayDuration']; ?>" class='navigable' src='<?php echo $hpc['redbuttonimage']; ?>' />
<?php 
    Mpat_Utilities::get_template_parts( array('frontend/parts/html-footer') );
?>
