(function($) {

	var $videoPlayer, videoObj, subtitleFiles = [];

	function initVideoPlayer() {

		$videoPlayer = $('#videoplayer');
		if ($videoPlayer.length) {
			videoObj = $('#videoplayer')[0];
		}

		$('param', $videoPlayer).each(function(index, obj) {
			var name = $(obj).attr('name'),
				value = $(obj).attr('value'),
				subtitlesIndex,
				subtitleKey;
			var regEx = /subtitles\[(\d+)\]\[(url|language)\]/g,
				matches;
			while ((matches = regEx.exec(name)) !== null) {
				if (matches.index === regEx.lastIndex) {
					regEx.lastIndex++;
				}
				subtitlesIndex = matches[1];
				subtitleKey = matches[2];
			}
			if (subtitlesIndex !== null) {
				if (!subtitleFiles[subtitlesIndex]) {
					subtitleFiles[subtitlesIndex] = {};
				}
				subtitleFiles[subtitlesIndex][subtitleKey] = value;
			}
		});

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
			renderSubtitles();
		}

	}

	function renderSubtitles() {
		// TODO: implement the continuos render mechanism for the subtitles
	}

	$(document).ready(function() {
		setTimeout(initVideoPlayer, 10);
	});

})(jQuery);
