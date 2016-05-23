<?php
/*
 Template Name: Single Media
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

$hpc = get_post_meta($post->ID, '_mpat_pageContent', TRUE);
if (!$hpc) {
    $hpc = array();
}

get_template_part('frontend/parts/html-header');
?>
<div id="content">
    <div class="content_primary <?php echo $hpc['primary']['navigable']; ?>"
         data-type="<?php echo $hpc['primary']['contenttype'] ?>">
        <?php generateContentBox($hpc['primary']) ?>
    </div>
    <div class="content_secondary <?php echo $hpc['secondary']['navigable']; ?>"
         data-type="<?php echo $hpc['secondary']['contenttype'] ?>">
        <?php generateContentBox($hpc['secondary']) ?>
    </div>
    <div class="content_advertisement <?php echo $hpc['advertisement']['navigable']; ?>"
         style="background-image:url(<?php echo $hpc['advertisement']['thumbnail']; ?>)">
        <a href="<?php echo $hpc['advertisement']['link']; ?>"></a>
    </div>
</div>
<?php
Mpat_Utilities::get_template_parts(array('frontend/parts/html-footer'));

?>