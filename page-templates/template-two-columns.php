<?php
/*
 Template Name: Two Columns
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

$boxview = $_GET['boxview'];
$hpc = get_post_meta($post->ID, '_mpat_pageContent', TRUE);
if (!$hpc) {
    $hpc = array();
}
if ($boxview) {
    echo generateContentBox($hpc[$boxview]);
} else {
    get_template_part('frontend/parts/html-header');
    get_template_part('frontend/parts/header');
    ?>
    <div id="content">
        <div class="content_twoColumn left <?php echo $hpc['box1']['navigable']; ?>"
             data-type="<?php echo $hpc['box1']['contenttype'] ?>">
            <?php generateContentBox($hpc['box1']) ?>
        </div>
        <div class="content_twoColumn right <?php echo $hpc['box2']['navigable']; ?>"
             data-type="<?php echo $hpc['box2']['contenttype'] ?>">
            <?php generateContentBox($hpc['box2']) ?>
        </div>
    </div>
    <?php
    Mpat_Utilities::get_template_parts(array('frontend/parts/footer', 'frontend/parts/html-footer'));

}
?>