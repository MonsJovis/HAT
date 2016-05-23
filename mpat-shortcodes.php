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
    function mpat_gallery_shortcode($params){
        $atts = shortcode_atts(array(
            'size'  => 'medium',
            'ids'   => '0'
        ),$params);
        ob_start();
        $img_ids = explode(',',$atts[ids]);
        ?>
        <div class='imageContent <?php echo $atts[size];?>' >
            <?php if ($atts[size]==='large' || $atts[size]==='full'){
            foreach ($img_ids as $index => $id) { ?>
                <div class='img-row'>
                    <div class='img-wrap' column='0' style='background-image:url("<?php echo wp_get_attachment_url($id);?>")'></div>
                </div>
            <?php }
            } else if ($atts[size]==='medium' || $atts[size]==='thumbnail'){
				foreach ($img_ids as $index => $id) {
					if ($index % 2 == 0) {?>
                        <div class='img-row'>
                    <?php } ?>
					<div class='img-wrap' column='<?php echo $index % 2;?>' style='background-image:url("<?php echo wp_get_attachment_url($id);?>")'></div>
					<?php if ($index % 2 == 1 || $index==count($img_ids)-1) { ?>
						</div>
					<?php }
				}
			} ?>
        </div>
        <?php
        return ob_get_clean();
    }
?>