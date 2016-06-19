(function($) {

	var $videoPlayer, videoObj, $subtitleItem, subtitles, subtitleFiles = [];

	function initVideoPlayer() {

		$videoPlayer = $('#videoplayer');
		$subtitleItem = $('.subtitle-item');
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

		readyCallback();

	}

	function readyCallback() {
		if ($videoPlayer && videoObj && videoObj.play && subtitles) {
			videoObj.play(1);
			$videoPlayer.trigger('play'); // TODO: trigger does not work
			for (var i = 0; i < subtitles.length; i++) {
				renderSubtitles(i);
			}
		}
	}

	function renderSubtitles(i) {
		setTimeout(function() {
			$subtitleItem.data('subtitle', i);
			$subtitleItem.html(subtitles[i].text).show();
		}, subtitles[i].begin);
		setTimeout(function() {
			if ($subtitleItem.data('subtitle') === i) {
				$subtitleItem.hide();
			}
		}, subtitles[i].end);
	}

	$(document).ready(function() {

		$videoPlayer = $('#videoplayer');
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

		if (subtitleFiles[0]) {
			$.get(subtitleFiles[0].url, function(data) {
				subtitles = parseSubtitles(data);
				readyCallback();
			}, 'text');
		}

		setTimeout(initVideoPlayer, 10);

	});
})(jQuery);
