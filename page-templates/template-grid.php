<?php
/*
  Template Name: Grid
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

$postMeta = get_post_meta( $post->ID, '_mpat_pageContent', true);
if(!$postMeta) { $postMeta = array(); }

Mpat_Utilities::get_template_parts(array('frontend/parts/html-header'));
?>
<div xmlns="http://www.w3.org/1999/xhtml" id="content">
    <div id="top-bar"></div>
    <div id="middle-bar"></div>
    <div id="app-list">
        <div id="scroll-container">
            <div class="scroll-spacer"></div>
            <?php
            if(!$postMeta[apps]) { $postMeta[apps] = array(); }
            foreach($postMeta[apps] as $value){?>
                <div style="background-image: url('<?php echo $value[thumbnail_url];?>')" class="app-item navigable">
                    <a href="<?php echo $value[app_url];?>"><?php echo $value[title];?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div id="active-app-over"></div>
    <div id="date">
        <?php
        date_default_timezone_set("Europe/Berlin");

        $weekday[0] = "Sonntag";
        $weekday[1] = "Montag";
        $weekday[2] = "Dienstag";
        $weekday[3] = "Mittwoch";
        $weekday[4] = "Donnerstag";
        $weekday[5] = "Freitag";
        $weekday[6] = "Samstag";

        $month[0] = "Januar";
        $month[1] = "Februar";
        $month[2] = "März";
        $month[3] = "April";
        $month[4] = "Mai";
        $month[5] = "Juni";
        $month[6] = "Juli";
        $month[7] = "August";
        $month[8] = "September";
        $month[9] = "Oktober";
        $month[10] = "November";
        $month[11] = "Dezember";

        $weekdaynum = date("w");
        $monthnum = date("m");
        $time = date("G.i");
        $year = date("Y");
        $day = date("j");
        echo strtoupper($weekday[$weekdaynum]).", ".$day.". ".strtoupper($month[$monthnum])." ".$year." <span class=\"time\">".$time." UHR</span>";
        ?>
    </div>
    <div id="left-arrow" style="left: 484px;"></div>
    <div id="right-arrow" style="left: 786px;"></div>
    <div id="bottom-bar"></div>
    <div id="left-area">
        <img src="http://static-cdn.arte.tv/redbutton/images/navig_DE.png" alt="" />
        <img src="http://static-cdn.arte.tv/redbutton/images/valid_DE.png" alt="" />
    </div>
    <div id="right-area">
        <div class="item">
            <span class="pict red"></span>
            BEENDEN
        </div>
        <div class="item">
            <span class="pict blue"></span>
            HILFE/IMPRESSUM
        </div>
    </div>
    <div id="logo"></div>
    <div id="nolink">undefined</div>
</div>
<?php
Mpat_Utilities::get_template_parts(array('frontend/parts/html-footer'));
?>


