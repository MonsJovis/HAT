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


//-----------------------------------------------------
//  Custom type meta boxes
//-----------------------------------------------------

function mpat_function_meta_box_callback($post, $param)
{
    ?>
    <textarea name="_mpat_functionContent" id="post_text"
              rows="10"><?php echo get_post_meta($post->ID, '_mpat_functionContent', true) ?></textarea>
    <?php
}

function mpat_gallery_item_meta_box_callback($post, $param)
{
    $postMeta = get_post_meta($post->ID, '_mpat_galleryItemContent', true);
    if (!$postMeta) {
        $postMeta = array();
    };
    ?>
    <style>
        #description-editor {
            left: 40%;
            width: 55%;
            margin-right: 5%;
            float: left;

        }

        #preview {
            margin: 0px 5%;
            left: 0%;
            width: 30%;
            float: left;
        }

        #content-edit {
            overflow: auto
        }

        #cover-preview {
            width: 100%;
        }

        #cover-preview img {
            width: 100%;
            background-size: cover;
        }
    </style>
    <div id="content-edit">
        <div id="description-editor">
            <?php
            wp_editor($postMeta['info'], 'editor', array('textarea_name' => '_mpat_galleryItemContent[info]', 'drag_drop_upload' => true, 'wpautop' => false));
            ?>
            <h4>Associated Galleries</h4>
            <div id="associated-galleries">
                <ul style="margin-left:30px;"><?php
                    $args = array(
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'tag__in' => wp_get_post_tags($post->ID, array('fields' => 'ids')),
                        'post_type' => 'page',
                        'post_status' => 'publish',
                        'suppress_filters' => true,
                        'meta_query' => array(
                            array(
                                'key' => '_wp_page_template',
                                'value' => 'template-media-gallery.php',
                                'compare' => '=',
                            ),
                        ),
                    );
                    // The Query
                    $items = new WP_Query($args);

                    // The Loop
                    if ($items->have_posts()) {
                        while ($items->have_posts()) {
                            $items->the_post();
                            ?>
                            <li>
                                <?php echo get_the_title(); ?> <a href="<?php echo get_edit_post_link($item->ID); ?>">edit</a>
                            </li>
                            <?php
                        }
                    } else {
                        // no posts found
                    }
                    /* Restore original Post Data */
                    wp_reset_postdata();
                    ?>
                </ul>
            </div>
        </div>
        <div id="preview">
            <label> Cover-URL <input type='text' name='_mpat_galleryItemContent[cover_url]'
                                     value="<?php echo $postMeta['cover_url']; ?>"> <br/><br/>or Cover-Upload: <input
                    type='button' class='media_upload' value='Select File'></label>
            <div id="cover-preview">
                <img src="<?php echo $postMeta['cover_url']; ?>"></img>
            </div>
            <label> Trailer-URL <input type='text' name='_mpat_galleryItemContent[trailer_url]'
                                       value="<?php echo $postMeta['trailer_url']; ?>"> <br/><br/> or Trailer-Upload:
                <input type='button' class='media_upload' value='Select File'></label>
        </div>

    </div>

    <?php
}

function mpat_popup_meta_box_callback($post, $param)
{
    $postMeta = get_post_meta($post->ID, '_mpat_popupContent', true);
    if (!$postMeta) {
        $postMeta = array();
    };
    ?>
    <p>
        In this section you can assign your JavaScript functions to buttons which are <strong>only</strong> available in
        this particular popup.
        The buttons will be shown on the footer of the popup-window.
    </p>
    <table id="buttonFunctions">
        <tr>
            <th>Button</th>
            <th>Function</th>
            <th>Activate</th>
        </tr>
        <?php
        for ($i = 0; $i < 10; $i++) {
            popup_button_function($i, $postMeta);
        }
        popup_button_function('Blue', $postMeta);
        popup_button_function('Green', $postMeta);
        popup_button_function('Yellow', $postMeta);
        ?>
    </table>
    <script>
        function toogleTableRow(target) {
            var opt = jQuery(target).parent().parent().find('select , input:not(.checkbox)');
            if (target.checked) {
                opt.removeAttr('disabled');
            } else {
                opt.attr('disabled', '');
            }
        }
    </script>
    <?php
}

//-----------------------------------------------------
//  Page meta boxes
//-----------------------------------------------------

function gallery_meta_box_callback($post, $param)
{
    $postMeta = get_post_meta($post->ID, '_mpat_pageContent', true);
    if (!$postMeta) {
        $postMeta = array();
    }
    ?>
    <label style="display: block;"><p style="width:90px;display:inline-block;margin:0;">Cover orientation: </p>
        <select name="_mpat_pageContent[cover_orientation]">
            <option selected value="portrait"<?php selected($postMeta['cover_orientation'], 'portrait'); ?>>Portrait
            </option>
            <option value="landscape"<?php selected($postMeta['cover_orientation'], 'landscape'); ?>>Landscape</option>
        </select>
    </label>
    <label style="display: block;"><p style="width:90px;display:inline-block;margin:0;">Item-list position</p>
        <select name="_mpat_pageContent[list_position]">
            <option selected value="left"<?php selected($postMeta['list_position'], 'left'); ?>>Left</option>
            <option value="right"<?php selected($postMeta['list_position'], 'right'); ?>>Right</option>
        </select>
    </label>
    <div>
        <h4>Associated Gallery Items</h4>
        <div id="associated-items">
            <ul style="margin-left:30px;"><?php
                $args = array(
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'tag__in' => wp_get_post_tags($post->ID, array('fields' => 'ids')),
                    'post_type' => 'mpat_gallery_item',
                    'post_status' => 'publish',
                    'suppress_filters' => true
                );
                $items = get_posts($args);
                foreach ($items as $item) { ?>
                    <li>
                        <?php echo $item->post_title; ?> <a href="<?php echo get_edit_post_link($item->ID); ?>">edit</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <?php
}

function grid_meta_box_callback($post, $param)
{

    function app_edit_callback($title,$app,$thumbnail){
        ?><li class="app-edit ui-state-default">
            <div>Title: <input class="title-input" type="text" name="title"
                               value="<?php echo $title; ?>"></div>
            <div>App-URL: <input class="app-url-input" type="text" name="app_url"
                                 value="<?php echo $app; ?>"></div>
            <div>Thumbnail-URL: <input class="thumbnail-url-input" type="text"
                                       name="thumbnail_url"
                                       value="<?php echo $thumbnail; ?>">
                <input type='button' class='media_upload' value='Select File'></div>
            <div alt="f153" class="dashicons dashicons-dismiss remove-app-button"></div>
        </li><?php
    }

    $meta_key = '_mpat_pageContent';
    $postMeta = get_post_meta($post->ID, $meta_key, true);
    if (!$postMeta) {
        $postMeta = array();
    }
    ?>
    <style>
        .remove-app-button{
            padding: 4px;
        }
        .remove-app-button:hover {
            color: #ff5555;
        }
        .app-edit div {
            float: left;
            margin-right:20px;
        }
        .app-edit {
            margin-bottom: 20px;
            padding: 10px;
            overflow: auto;
            display: inline-block;
        }

        .app-edit .title-input {
            width: 100px;
        }
    </style>
    <div id="input-dummy"></div>
    <ul id="app-add">
        <?php
        if (!$postMeta[apps]) $postMeta[apps] = array();
        foreach ($postMeta[apps] as $value) {
            app_edit_callback($value[title],$value[app_url],$value[thumbnail_url]);
        } ?>
    </ul>
    <?php submit_button('Add another app', array('secondary', 'large'), 'add-app-button'); ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#app-add').change(function(){
                $('.remove-app-button').click(function(){
                    $(this).parent().remove();
                });
                $(this).sortable();
            }).change();

            $('input[type="submit"]').click(function () { //listen for submit event
                $('.app-edit').each(function (index) {
                    var app_url = $(this).find('.app-url-input').val();
                    var thumbnail_url = $(this).find('.thumbnail-url-input').val();
                    var title = $(this).find('.title-input').val();
                    $('<input />').attr('type', 'hidden')
                        .attr('name', '<?php echo $meta_key;?>[apps][' + index + '][title]')
                        .attr('value', title)
                        .appendTo('#input-dummy');
                    $('<input />').attr('type', 'hidden')
                        .attr('name', '<?php echo $meta_key;?>[apps][' + index + '][app_url]')
                        .attr('value', app_url)
                        .appendTo('#input-dummy');
                    $('<input />').attr('type', 'hidden')
                        .attr('name', '<?php echo $meta_key;?>[apps][' + index + '][thumbnail_url]')
                        .attr('value', thumbnail_url)
                        .appendTo('#input-dummy');
                });
                return true;
            });

            $("#add-app-button").click(function (event) {
                event.preventDefault();
                var el = $(
                    <?php ob_start();
                    app_edit_callback("","","");
                    ob_get_clean_json() ?>
                );
                $('#app-add').append(el).change();
            });
        });
    </script>
    <?php
}

function red_button_meta_box_callback($post, $param){
    global $page_content_key;
    $meta_key = '_mpat_pageContent';
    $postMeta = get_post_meta($post->ID, $meta_key, true);
    if (!$postMeta) {
        $postMeta = array();
    }
    ?>

    Frontpage-URL:
    <input style="margin-left:12px" type="text" size="50" value="<?php echo $postMeta['url'] ?>" name="<?php echo $page_content_key?>[url]">
    <br>Fade-In Time:
    <input style="margin-left:24px" type="text" size="3" value="<?php echo $postMeta['fadeinTime'] ?>" name="<?php echo $page_content_key?>[fadeinTime]">
    <br>Display Duration:
    <input type="text" size="3" value="<?php echo $postMeta['displayDuration'] ?>" name="<?php echo $page_content_key?>[displayDuration]">

    <div>
    <br>
    Redbutton Image <input type='button' data-uploadertype="redbutton" class='media_upload' value='Select/Change Image'>
    <br>URL: <input style="margin-left:78px" type="text" size="50" value="<?php echo $postMeta['redbuttonimage'] ?>" name="<?php echo $page_content_key?>[redbuttonimage]">
    <br>Preview:
        <div style="margin-left:109px;margin-top:-17px;width:250px;height:150px;background-image:url(<?php echo $postMeta['redbutonimage'] ?>);background-repeat:no-repeat;background-size:contain;background-position:center;border:1px solid lightgray;" >
        </div>
    </div>

    <?php
}

function meta_box_advertisement_callback($post, $param){
    $meta_key = '_mpat_pageContent';
    $postMeta = get_post_meta($post->ID, $meta_key, true);
    if (!$postMeta) {
        $postMeta = array();
    } else {
        $postMeta = $postMeta['advertisement'];
    }
    ?>
    <div>Link-URL: <input class="link-input" type="text" name="_mpat_pageContent[advertisement][link]"
                         value="<?php echo $postMeta['link']; ?>"></div>
    <div>Thumbnail-URL: <input class="thumbnail-input" type="text"
                               name="_mpat_pageContent[advertisement][thumbnail]"
                               value="<?php echo $postMeta['thumbnail']; ?>">
        <input type='button' class='media_upload' value='Select File'></div>
    <label style="display: block;"><p style="width:90px;display:inline-block;margin:0;">Navigable:</p>
        <input
            type="checkbox" name="_mpat_pageContent[advertisement][navigable]"
            value="navigable" <?php if ($postMeta['navigable']) {
            echo checked;
        } ?>>
    </label>
    <?php
}

function meta_box_layout_callback($post, $param)
{
    global $page_content_key;
    $postMeta = get_post_meta($post->ID, $page_content_key, true);
    if (!$postMeta) {
        $postMeta = array();
    } else {
        $postMeta = $postMeta['layout'];
    }
    ?>
    <label style="display: block;"><p style="width:90px;display:inline-block;margin:0;">Box Width</p>
        <input style="width: 50px;" name="<?php echo $page_content_key?>[layout][width]" type="number" min="33" max="100" step="1" value="<?php echo $postMeta['width'];?>"/> %
    </label>
    <label style="display: block;"><p style="width:90px;display:inline-block;margin:0;">Box Aligment</p>
        <select name="<?php echo $page_content_key?>[layout][align] ?>]">
            <option disabled selected> -- select an option -- </option>
            <option value="left"<?php selected( $postMeta['align'], 'left' ); ?>>Left</option>
            <option value="center"<?php selected( $postMeta['align'], 'center' ); ?>>Center</option>
            <option value="right"<?php selected( $postMeta['align'], 'right' ); ?>>Right</option>
        </select>
    </label>
    <?php

}

$page_content_key = '_mpat_pageContent';

function content_text_callback($postMeta, $box){
    //TODO remove numbers from box name, because according to the documentation editor id only works with lowercase letters
    global $page_content_key;
    $content = isset($postMeta[data]) ? $postMeta[data] : "Enter text here";
    $args = array(
        'textarea_name' => $page_content_key . '['.$box.'][data]',
        'drag_drop_upload' => true,
        'wpautop' => false
    );
    wp_editor($content, 'textedit_' . $box, $args);
}

function content_images_callback($postMeta, $box){
    global $page_content_key;
    media_buttons("textedit_imgs_".$box);
    ?><input type="hidden" id="textedit_imgs_<?php echo $box ?>"
           name="<?php echo $page_content_key.'['.$box .']'; ?>[data]"
           value="<?php echo htmlspecialchars($postMeta[data]);?>"><?php
}

function content_broadcast_callback($postMeta,$box){

}

function content_video_callback($postMeta, $box){
    global $page_content_key;

    $vtts = array();
    foreach ($postMeta as $key => $value) {
      if (strstr($key, 'vtt_') !== false) {
        $vtts[] = $value;
      }
    }
?>
    Video-URL: <input type='text' name='<?php echo $page_content_key;?>[<?php echo $box ?>][data]' value="<?php echo $postMeta['data']; ?>">
    <br>
    <br>
    or <br>
    <br>File-Upload: <input type='button' class='media_upload' value='Select File'>
    <br>
    <br>
    <hr>
    <br>
    Subtitles:<br>

    <?php ob_start(); ?>
    <div class="subtitle_file">
      <input type="hidden" name="<?php echo $page_content_key;?>[<?php echo $box ?>][vtt_{{NO}}][attachment_id]" value="" />
      <label></label>
      <?php print mpat_lang_select($page_content_key.'['.$box.'][vtt_{{NO}}][language]') ?>
      <span class="dashicons dashicons-trash"></span>
    </div>
    <?php $subtitleTemplate = ob_get_contents(); ?>
    <?php ob_end_clean(); ?>

    <div class="subtitle-files-outer">

      <?php foreach($vtts as $index => $vtt): ?>
        <div class="subtitle-file">
          <input type="hidden" name="<?php echo $page_content_key.'['.$box.'][vtt_'.$index.'][attachment_id]'?>" value="<?php print $vtt['attachment_id'] ?>" />
          <label><?php print basename( get_attached_file( $vtt['attachment_id'] ) ); ?></label>
          <?php print mpat_lang_select($page_content_key.'['.$box.'][vtt_'.$index.'][language]', $vtt['language']) ?>
          <span class="dashicons dashicons-trash"></span>
        </div>
      <?php endforeach ?>

    </div>
    <input id="vtt_attachments_button" type="button" value="Select or upload a subtitle file" />

    <script>
      (function($) {

        var media_uploader = null;
        var subtitleTemplate = '<?php print json_encode($subtitleTemplate); ?>';

        function open_media_uploader_video() {

          media_uploader = wp.media({
            title: 'Select or upload a subitle file (WebVTT)',
            button: {
              text: 'Select'
            },
            multiple: true
          });

          media_uploader.on("select", function() {
            var totalIndex = $('.subtitle-file').length;
            var attachments = media_uploader.state().get('selection').toJSON();
            $.each(attachments, function(index, attachment) {
              var $file = $(subtitleTemplate);
              $('input[type=hidden]', $file).val(attachment.id);
              $('input[type=hidden]', $file).attr('name', $('input[type=hidden]', $file).attr('name').replace('{{NO}}', totalIndex));
              $('label', $file).html(attachment.filename);
              $('select', $file).attr('name', $('select', $file).attr('name').replace('{{NO}}', totalIndex));
              $('.dashicons-trash', $file).click(function() {
                $file.remove();
                totalIndex--;
              });
              $file.show();
              $file.appendTo($('.subtitle-files-outer')[0]);
              totalIndex++;
            });
          });

          media_uploader.open();

        }

        $('#vtt_attachments_button').click(open_media_uploader_video);

        $('.subtitle-file .dashicons-trash').click(function() {
          $(this).parent().remove();
        });

       }(jQuery));

    </script>
<?php
}


function mpat_lang_select($name = 'language', $defaultValue = null) {
  $fileContent = file_get_contents(get_template_directory() . '/assets/languages.json', 'r');
  $json = json_decode($fileContent);
  ob_start();
?>
  <select name="<?php print $name ?>">
    <option value=""><?php print __('- Select the language -', 'mpat') ?></option>
    <?php foreach ($json->lang as $key => $values): ?>
      <option value="<?php print $key ?>"<?php if ($defaultValue == $key) print ' selected' ?>><?php print $values[0] ?></option>
    <?php endforeach ?>
  </select>
<?php
  $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

function content_scribble_callback($postMeta, $box){
    global $page_content_key;
    ?>Scribble ID: <input type="text" name="<?php echo $page_content_key.'['.$box.']';?>[data]'"
                                        value="<?php echo $postMeta[data]; ?>"><?php
}

function content_social_callback($postMeta,$box){
    global $page_content_key;
    ?>Social ID: <input type="text" name="<?php echo $page_content_key.'['.$box.']';?>[data]'"
                          value="<?php echo $postMeta[data]; ?>"><?php
}

function content_360_callback($postMeta,$box){
    global $page_content_key;
    $server_url = $postMeta['data']['server_url'];
    if (empty($server_url)) $server_url = 'http://193.174.152.28';
    ?>
    360° Videos:
    <select name="<?php echo $page_content_key?>[<?php echo $box ?>][data][video]">
        <option disabled selected>-- select a  360° Video --</option>
        <option value="{ id: 'MontBlanc_DE_360_master4K', fps: 30, quality: 6 }" <?php selected($postMeta['data']['video'], "{ id: 'MontBlanc_DE_360_master4K', fps: 30, quality: 6 }"); ?>>MontBlanc_DE_360_master4K</option>
        <option value="{ id: 'p7pilot', fps: 30, quality: 8 }" <?php selected($postMeta['data']['video'], "{ id: 'p7pilot', fps: 30, quality: 8 }"); ?>>p7pilot</option>
        <option value="{ id: 'La_Scala_UPRES_3840x1920_30FPS_CBR_35mbit', fps: 30, quality: 6 }" <?php selected($postMeta['data']['video'], "{ id: 'La_Scala_UPRES_3840x1920_30FPS_CBR_35mbit', fps: 30, quality: 6 }");?>>La_Scala_UPRES_3840x1920_30FPS_CBR_35mbit</option>
        <option value="{ id: 'mws2015', fps: 25, quality: 8 }" <?php selected($postMeta['data']['video'], "{ id: 'mws2015', fps: 25, quality: 8 }");?>>mws2015</option>
        <option value="{ id: 'Elisir_damor_V5_master4K', fps: 30, quality: 6 }" <?php selected($postMeta['data']['video'], "{ id: 'Elisir_damor_V5_master4K', fps: 30, quality: 6 }");?>>Elisir_damor_V5_master4K</option>
        <option value="{ id: 'terraXfinal', fps: 25, quality: 8 }" <?php selected($postMeta['data']['video'], "{ id: 'terraXfinal', fps: 25, quality: 8 }");?>>terraXfinal</option>
        <option value="{ id: 'corvette4k', fps: 30, quality: 8}" <?php selected($postMeta['data']['video'], "{ id: 'corvette4k', fps: 30, quality: 8}");?>>corvette4k</option>
        <option value="{ id: 'Carrara_V8_DE_360_master4K', fps: 30, quality: 6 }" <?php selected($postMeta['data']['video'], "{ id: 'Carrara_V8_DE_360_master4K', fps: 30, quality: 6 }");?>>Carrara_V8_DE_360_master4K</option>
        <option value="{ id: 'STRATOS_RC13_DE_Master4K', fps: 30, quality: 6 }" <?php selected($postMeta['data']['video'], "{ id: 'STRATOS_RC13_DE_Master4K', fps: 30, quality: 6 }");?>>STRATOS_RC13_DE_Master4K</option>
        <option value="{ id: 'TPS_trailer', fps: 30, quality: 6 }" <?php selected($postMeta['data']['video'], "{ id: 'TPS_trailer', fps: 30, quality: 6 }");?>>TPS_trailer</option>
        <option value="{ id: 'Berlin4k', fps: 25, quality: 8 }" <?php selected($postMeta['data']['video'], "{ id: 'Berlin4k', fps: 25, quality: 8 }");?>>Berlin4k</option>
    </select>
    Server-URL:
    <input type="text" value="<?php echo $server_url ?>" name="<?php echo $page_content_key?>[<?php echo $box ?>][data][server_url]">
    <?php
}

function contentselection_header_callback($postMeta, $box){
    global $page_content_key;
    ?><div style="float: right; max-width:48%;">
    <label style="display: block;"><p style="width:90px;display:inline-block;margin:0;">Contenttype:</p>
        <select class="contenttype <?php echo $box ?>" name="<?php echo $page_content_key?>[<?php echo $box ?>][contenttype]">
            <option disabled selected>-- select an option --</option>
            <option value="broadcast"<?php selected($postMeta['contenttype'], 'broadcast'); ?>>Broadcast</option>
            <option value="video"<?php selected($postMeta['contenttype'], 'video'); ?>>Video</option>
            <option value="text"<?php selected($postMeta['contenttype'], 'text'); ?>>Text</option>
            <option value="image"<?php selected($postMeta['contenttype'], 'image'); ?>>Images</option>
            <option value="scribble"<?php selected($postMeta['contenttype'], 'scribble'); ?>>Scribble</option>
            <option value="social"<?php selected($postMeta['contenttype'], 'social'); ?>>Social</option>
            <option value="360"<?php selected($postMeta['contenttype'], '360'); ?>>360° Video</option>
        </select>
    </label>
    <label style="display: block;"><p style="width:90px;display:inline-block;margin:0;">Navigable:</p>
        <input
            id="nav-checkbox-<?php echo $box ?>" <?php disabled($postMeta['contenttype'], 'broadcast'); ?>
            type="checkbox" name="<?php echo $page_content_key?>[<?php echo $box ?>][navigable]"
            value="navigable" <?php if ($postMeta['navigable']) {
            echo checked;
        } ?>>
    </label>
    <label style="display: block;"><p style="width:90px;display:inline-block;margin:0;">Title:</p>
        <input type="text" name="<?php echo $page_content_key?>[<?php echo $box ?>][title]" size="22"
               maxlength="40" value="<?php echo $postMeta['title'] ?>">
    </label>
    </div><?php
}

function meta_box_contentselection_callback($post, $param)
{
    global $page_content_key;
    $contenttype = $navigable = $title = $data = "";
    $postMeta = get_post_meta($post->ID, $page_content_key, true);
    $box = $param['args']['box'];
    if (!$postMeta) {
        $postMeta = array();
    } else {
        $postMeta = $postMeta[$box];
        $contenttype = $postMeta['contenttype'];
    }

    ?><img src="<?php echo $param['args']['iconurl'] ?>" style="width:48%; max-width:250px;min-height:80px;"></img><?php
    contentselection_header_callback($postMeta,$box);
    //TODO look for appeareances of class "other"
    ?><div class="settings" style="padding: 15px;">
        <div style="display:none" class="editor <?php echo $box;?>">
        <?php content_text_callback($postMeta, $box); ?>
        </div>
        <div class="other">

        </div><?php
        submit_button();
    ?></div>
    <script type="text/javascript">
        jQuery('.contenttype.<?php echo $param['args']['box'] ?>').change(function () {
            var html;
            var editor = jQuery('.settings .editor.<?php echo $param['args']['box'] ?>');
            editor.hide();
            switch (this.value) {
                case 'broadcast':
                    html = <?php ob_start();
                    content_broadcast_callback($postMeta, $box);
                    ob_get_clean_json();?>;
                    jQuery('#nav-checkbox-<?php echo $param['args']['box'] ?>').attr('disabled', 'true');
                    break;
                case 'video':
                    html = <?php ob_start();
                    content_video_callback($postMeta, $box);
                    ob_get_clean_json();?>;
                    jQuery('#nav-checkbox-<?php echo $param['args']['box'] ?>').removeAttr('disabled');
                    break;
                case 'text':
                    html = "";
                    editor.show();
                    jQuery('#nav-checkbox-<?php echo $param['args']['box'] ?>').removeAttr('disabled');
                    break;
                case 'image':
                    html = <?php ob_start();
                    content_images_callback($postMeta, $box);
                    ob_get_clean_json();?>;
                    jQuery('#nav-checkbox-<?php echo $param['args']['box'] ?>').removeAttr('disabled');
                    break;
                case 'scribble':
                    html = <?php ob_start();
                    content_scribble_callback($postMeta, $box);
                    ob_get_clean_json();?>;
                    jQuery('#nav-checkbox-<?php echo $param['args']['box'] ?>').removeAttr('disabled');
                    break;
                case 'social':
                    html = <?php ob_start();
                    content_social_callback($postMeta, $box);
                    ob_get_clean_json();?>;
                    jQuery('#nav-checkbox-<?php echo $param['args']['box'] ?>').removeAttr('disabled');
                    break;
                case '360':
                    html = <?php ob_start();
                    content_360_callback($postMeta, $box);
                    ob_get_clean_json();?>;
                    jQuery('#nav-checkbox-<?php echo $param['args']['box'] ?>').removeAttr('disabled');
                default:
            }
            jQuery(this).eq(0).parent().parent().next().children('.other').html(html);
        }).change();
    </script>
    <?php
}

//Called when you press save on post editor
add_action('save_post', 'save_meta_box_contentselection');

function save_meta_box_contentselection($post_id)
{

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post')) return;

    if (get_post_type($post_id) == 'mpat_popup') {
        $new_meta_value = $_POST['_mpat_popupContent'];
        $meta_key = '_mpat_popupContent';
        $meta_value = get_post_meta($post_id, $meta_key, true);
        if ($new_meta_value && $new_meta_value != $meta_value) {
            update_post_meta($post_id, $meta_key, $new_meta_value);
        }
    } elseif (get_post_type($post_id) == 'mpat_function') {
        $new_meta_value = $_POST['_mpat_functionContent'];
        $meta_key = '_mpat_functionContent';
        $meta_value = get_post_meta($post_id, $meta_key, true);
        if ($new_meta_value && $new_meta_value != $meta_value) {
            update_post_meta($post_id, $meta_key, $new_meta_value);
        }

    } elseif (get_post_type($post_id) == 'mpat_gallery_item') {
        $new_meta_value = $_POST['_mpat_galleryItemContent'];
        $meta_key = '_mpat_galleryItemContent';
        $meta_value = get_post_meta($post_id, $meta_key, true);
        if ($new_meta_value && $new_meta_value != $meta_value) {
            update_post_meta($post_id, $meta_key, $new_meta_value);
        }

    } else {
        $new_meta_value = $_POST['_mpat_pageContent'];
        $meta_key = '_mpat_pageContent';
        $meta_value = get_post_meta($post_id, $meta_key, true);
        if ($new_meta_value && $new_meta_value != $meta_value) {
            update_post_meta($post_id, $meta_key, $new_meta_value);
        }
    }

}

//Utils
function popup_button_function($buttonName, $meta)
{

    $func_id = $meta['button_functions'][$buttonName];
    $exist = isset($func_id);
    ?>
    <tr>
        <th><img
                src="<?php echo(get_bloginfo('template_url') . '/shared/assets/button' . $buttonName . '.png') ?>"></img>
        </th>
        <th>
            <select <?php disabled(!$exist); ?> class="functionSelection"
                                                name="_mpat_popupContent[button_functions][<?php echo $buttonName ?>]">
                <option selected disabled> Select a function</option>
                <?php
                $args = array(
                    'posts_per_page' => 5,
                    'offset' => 0,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_type' => 'mpat_function',
                    'post_status' => 'publish',
                    'suppress_filters' => true
                );
                $funcs = get_posts($args);
                foreach ($funcs as $f) { ?>
                    <option
                        value="<?php echo $f->ID; ?>" <?php selected($f->ID, $func_id); ?>><?php echo $f->post_title; ?></option>
                    <?php
                }
                ?>
            </select>
        </th>
        <th>
            <input class="checkbox" type="checkbox" onchange="toogleTableRow(this)" <?php checked($exist); ?>>
        </th>

    </tr>
    <?php
}

function generateContentBox($data)
{
    $html="";
    switch ($data['contenttype']):
        case 'broadcast':
            $html .= "<object id='broadcast' type='video/broadcast' height='100%' width='100%'></object>";
            $html .= "<script type='text/javascript'>jQuery(document).ready(function($){window.setTimeout( function() { $('#broadcast')[0].bindToCurrentChannel(); }, 10); });</script>";
            break;
        case 'video':
            $subtitleSettings = mpat_get_subtitle_settings($data);
            $html .= "<div style='display:none' class='video-dummy' vid='$data[data]'></div>";
            $html .= '<div class="videoplayer-outer">';
            $html .= '<div class="subtitle-item"></div>';
            $html .= "<object id='videoplayer' width='100%' height='100%' type='video/mp4' data='$data[data]'>";
            foreach ($data as $key => $value) {
              if (strstr($key, 'vtt_') !== false) {
                $subtitleIndex = substr($key, 4);
                if ($url = wp_get_attachment_url($value['attachment_id'])) {
                  $html .= "<param name='subtitles[$subtitleIndex][url]' value='$url' />";
                  if ($language = $value['language']) {
                    $html .= "<param name='subtitles[$subtitleIndex][language]' value='$language' />";
                  }
                }
              }
            }
            $html .= '</object>';
            ob_start();
?>
  <div class="subtitle-menu-outer">
    <ul class="subtitle-menu subtitle-main-menu" style="display:none">
      <li class="back">Subtitles</li>
      <?php foreach ($subtitleSettings as $settingKey => $setting): ?>
        <li data-setting="<?php print $settingKey ?>"><?php print $setting['label'] ?> <img class="arrow-right" style="height:15px" src="<?php print get_template_directory_uri () ?>/frontend/img/arrow.png" /></li>
      <?php endforeach ?>
    </ul>
    <?php foreach ($subtitleSettings as $settingKey => $setting): ?>
      <ul class="subtitle-menu subtitle-submenu subtitle-submenu-<?php print $settingKey ?>" data-setting="<?php print $settingKey ?>" style="display:none">
        <li class="back"><img class="arrow-left" style="height:15px" src="<?php print get_template_directory_uri () ?>/frontend/img/arrow.png" /> <?php print $setting['label'] ?></li>
        <?php foreach ($setting['values'] as $key => $label): ?>
          <li data-value="<?php print $key ?>"<?php if ($key == $setting['default']): ?> class="enabled"<?php endif ?>>
            <?php print $label ?>
            <img class="icon-checked" style="height:25px" src="<?php print get_template_directory_uri () ?>/frontend/img/checkmark.png" />
          </li>
        <?php endforeach ?>
      </ul>
    <?php endforeach ?>
  </div>
<?php
            $html .= ob_get_contents();
            ob_end_clean();
            $html .= '</div>';
            break;
        case 'image':
            $html .= "<div class='contentHeader'>$data[title]</div><div>";
            $html .= do_shortcode($data[data]);
            $html .= "</div>";
            break;
        case 'text':
            $html .= "<div class='contentHeader'>$data[title]</div>";
            $html .= "<div class='textContent'>" . do_shortcode($data['data']) . "</div>";
            break;
        case 'scribble':
            $html .= "<script type='text/javascript'> jQuery(document).ready(function($){scribble = new scribbleModule($('[data-type=scribble]')[0], $data[data], '$data[title]'); scribble.setActive(true); }); </script>";
            break;
        case 'social':
            $html .= "<script type='text/javascript'> jQuery(document).ready(function($){ SocialModule.init('[data-type=social]', $data[data], '$data[title]')}); </script>";
            break;
        case '360':
            ob_start();?>
            <div class="video-360-ui-container">
                <div id='container-video-360' style='position: absolute; width: 100%; height: 100%;'></div>
                <div class="seek" id="controls-video-360">
                    <div>
                        <div class="button-360 label control-button-360" id="button-360-zoomin">-</div>
                        <div class="button-360 label control-button-360" id="button-360-zoomout">+</div>
                        <div id="button-360-stop" class="control-button-360"> </div>
                        <div id="button-360-toogleplay" class="control-button-360"> </div>
                        <div id="button-360-fullscreen" class="control-button-360"> </div>
                    </div>
                    <div id="seeker-360">
                        <div id="player-time-elapsed">00:00</div>
                        <div style="width: 275px;" id="player-track">
                            <div style="width: 0%;" id="player-track-elapsed"> </div>
                        </div>
                        <div id="player-time-full">00:00</div>
                    </div>
                    <div style="visibility: hidden; left: 231px;">Vollbildansicht</div>
                </div>
                <div class="arrows">
                    <div style="display:none" class="arrow arrow-left"></div>
                    <div style="display:none" class="arrow arrow-right"></div>
                    <div style="display:none" class="arrow arrow-up"></div>
                    <div style="display:none" class="arrow arrow-down"></div>
                </div>
            </div>
            <script type='text/javascript'>
                jQuery(document).ready(function($){controller360 = new Controller360().init(); setTimeout(function() { controller360.play(<?php echo $data['data']['video'];?>); }, 100);});
            </script>
            <?php
            $html .= ob_get_clean();

    endswitch;
    echo $html;
}

/**
 * Adds a box showing the Layout of the Post
 */

$current_template;

function all_types_add_layout_meta_box()
{
    global $current_template;
    $post_id = empty($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID'])?$_POST['post_ID']:"");
    $current_template = pathinfo(get_page_template_slug($post_id),PATHINFO_FILENAME);

    $screens = array('post', 'page');

    foreach ($screens as $screen) {

        add_meta_box(
            'myplugin_sectionid',
            __('Design of ' . $current_template, 'mpat'),
            'all_types_layout_meta_box_callback',
            $screen
        );
    }
}

add_action('add_meta_boxes', 'all_types_add_layout_meta_box');

function all_types_layout_meta_box_callback($post)
{
    global $current_template;
    $template_mockup = get_template_directory_uri() . '/backend/assets/' . $current_template . '_mockup.png';
    echo '<img src="' . $template_mockup . '" style="width:100%" ></img>';

}

function ob_get_clean_json(){
    echo json_encode(ob_get_clean());
}

function active($a,$b){
    if ($a === $b) echo 'active';
}

function mpat_get_subtitle_settings($postData) {
  $settings = array(
    'language' => array(
      'label' => __('Language', 'mpat'),
      'values' => array(),
      'default' => 0
    ),
    'font-size' => array(
      'label' => __('Font size', 'mpat'),
      'values' => array(
        'small' => __('Small', 'mpat'),
        'medium' => __('Medium', 'mpat'),
        'big' => __('Big', 'mpat')
      ),
      'default' => 'medium'
    ),
    'font-color' => array(
      'label' => __('Font color', 'mpat'),
      'values' => array(
        'white' => __('White', 'mpat'),
        'black' => __('Black', 'mpat'),
        'red' => __('Red', 'mpat'),
        'yellow' => __('Yellow', 'mpat')
      ),
      'default' => 'white'
    ),
    'background-color' => array(
      'label' => __('Background color', 'mpat'),
      'values' => array(
        'transparent' => __('Transparent', 'mpat'),
        'white' => __('White', 'mpat'),
        'black' => __('Black', 'mpat'),
        'red' => __('Red', 'mpat'),
        'yellow' => __('Yellow', 'mpat')
      ),
      'default' => 'transparent'
    ),
    'position' => array(
      'label' => __('Position', 'mpat'),
      'values' => array(
        'bottom' => __('Bottom', 'mpat'),
        'top' => __('Top', 'mpat')
      ),
      'default' => 'bottom'
    )
  );
  $languagesString = file_get_contents(get_template_directory() . '/assets/languages.json', 'r');
  $languages = json_decode($languagesString);
  foreach ($postData as $key => $value) {
    if (strstr($key, 'vtt_') !== false) {
      $subtitleIndex = substr($key, 4);
      if ($url = wp_get_attachment_url($value['attachment_id'])) {
        if ($language = $value['language']) {
          $lang = $languages->lang->$language;
          $settings['language']['values'][$language] = $lang[1];
        }
      }
    }
  }
  if (count($settings['language']['values']) > 0) {
    $firstItem = array_slice($settings['language']['values'], 0, NULL, true);
    $settings['language']['default'] = key($firstItem);
  }
  return $settings;
}
