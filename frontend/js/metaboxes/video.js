(function($) {

	var $videoPlayer, videoObj, subtitles;

	function initVideoPlayer() {

    $videoPlayer = $('#videoplayer');
    if ($videoPlayer.length) {
      videoObj = $('#videoplayer')[0];
    }

		$videoPlayer.on('play', function() {
			console.log('play');
		});
		$videoPlayer.on('playing', function() {
			console.log('playing');
		});
		$videoPlayer.on('progress', function() {
			console.log('progress');
		});
		$videoPlayer.on('pause', function() {
			console.log('pause');
		});
		$videoPlayer.on('error', function() {
			console.log('error');
		});
		$videoPlayer.on('ended', function() {
			console.log('ended');
		});
		$videoPlayer.on('waiting', function() {
			console.log('waiting');
		});
		$videoPlayer.on('seeking', function() {
			console.log('seeking');
		});
		$videoPlayer.on('seeked', function() {
			console.log('seeked');
		});

		if (videoObj && videoObj.play) {
			videoObj.play(1);
      $videoPlayer.trigger('play'); // TODO: trigger does not work

		}
		for (var i=0; i<subtitles.length; i++){
			renderSubtitles(i);
		}
  }

	function renderSubtitles(i) {
	  setTimeout(function() {
		  console.log( subtitles[i].begin + " " + subtitles[i].text);
	  }, subtitles[i].begin);

  }

	$(document).ready(function() {
		setTimeout(initVideoPlayer, 10);
		jQuery.get( "/wp-content/themes/HAT/sample_subtitles", function(data){
			subtitles = parseSubtitles(data);
		});

	});

})(jQuery);
