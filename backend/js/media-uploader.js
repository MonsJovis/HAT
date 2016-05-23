 /**
 * Backend Media Uploader
 *
*/

//Media Upload
//TODO - need some layout for the metabox upload
jQuery(document).ready(function($){
   var currentMediaUploader;
	$(this).change(function(){
       $(this).on('click', '.media_upload', function() {
          currentMediaUploader = jQuery(this);
          tb_show('','media-upload.php?TB_iframe=true');
          return false;
       });
    }).change();
   if((window.original_tb_remove == undefined) && (window.tb_remove != undefined)) {
      window.original_tb_remove = window.tb_remove;
      window.tb_remove = function() {
         window.original_tb_remove();
      };
   }
   window.original_send_to_editor = window.send_to_editor;
   window.send_to_editor = function(html) {
      console.log("send to editor: " + html);
      if(currentMediaUploader){
        if(currentMediaUploader.data("uploadertype") == "redbutton"){
          currentMediaUploader.next().next().attr("value", $(html).attr("src"));
          currentMediaUploader.next().next().next().next().css("background-image", "url("+$(html).attr("src")+")");

          // currentMediaUploader.nextAll("input:first").attr("value", $(html).attr("src"));
          // currentMediaUploader.nextAll("div:first").css("background-image", "url("+$(html).attr("src")+")");
          
        }else if(currentMediaUploader.data("uploadertype") == "360"){

        }else{
          var url = jQuery(html).attr('href');
          if (!currentMediaUploader.siblings("input:first").attr("value", url).length){
            var textarea = currentMediaUploader.siblings("textarea:first");
            if ($.trim(textarea.val())){
               textarea.append(",\n"+url);
            } else {
               textarea.append(url);
            }
          }
        }
      }else{
         window.original_send_to_editor(html);
      }
      tb_remove();
   }
});