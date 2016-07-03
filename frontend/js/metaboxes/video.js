(function($) {

  function initSubtitleMenu() {

    $('.subtitle-menu-btn').click(function() {
      $('.subtitle-main-menu').toggle();
      $('.subtitle-submenu').hide();
    });

    $('.subtitle-main-menu li').click(function() {
      var setting = $(this).attr('data-setting');
      $('.subtitle-main-menu').hide();
      $('.subtitle-submenu-'+setting).show();
    });

    $('.subtitle-submenu .back').click(function() {
      var setting = $(this).attr('data-setting');
      $('.subtitle-submenu').hide();
      $('.subtitle-main-menu').show();
    });

    $('.subtitle-submenu li').click(function() {
      var settingValue = $(this).attr('data-value');
      if (settingValue === undefined) return;
      $(this).parent().find('.fa-check').hide();
      $(this).find('.fa-check').show();
    });

  }

	var $videoPlayer,
		videoObj,
		$subtitleItem,
		subtitles,
		subtitleFiles = [],
		timeouts = [],
		isFireHbb = false;

	function initVideoPlayer() {

		log('initVideoPlayer');

		$videoPlayer = $('#videoplayer');
		$subtitleItem = $('.subtitle-item');
		if ($videoPlayer.length) {
			videoObj = $('#videoplayer')[0];
		}

		$videoPlayer.on('play', function() {
			log('play');
			setAllTimouts();
		});
		$videoPlayer.on('pause', function() {
			log('pause');
			clearAllTimeouts();
		});
		$videoPlayer.on('ended', function() {
			log('ended');
		});
		$videoPlayer.on('seeked', function() {
			log('seeked');
			clearAllTimeouts();
			setAllTimouts();
		});

		readyCallback();

	}

	function setSubtitleTimeout(subtitle) {
		var showttl = subtitle.begin - videoObj.currentTime * 1000,
			hidettl = subtitle.end - videoObj.currentTime * 1000;
		if (hidettl < 0) {
			return;
		} else if (showttl < 0 && hidettl > 0) {
			showttl = 0;
		}
		timeouts.push(setTimeout(function() {
			$subtitleItem.data('subtitle', subtitle.id);
			$subtitleItem.html(subtitle.text).show();
		}, showttl));
		timeouts.push(setTimeout(function() {
			if ($subtitleItem.data('subtitle') === subtitle.id) {
				$subtitleItem.hide();
			}
		}, hidettl));
	}

	function setAllTimouts() {
		for (var i = 0; i < subtitles.length; i++) {
			setSubtitleTimeout(subtitles[i]);
		}
	}

	function clearAllTimeouts() {
		while (timeouts.length) {
			clearTimeout(timeouts.pop());
		}
	}

	function readyCallback() {
		if ($videoPlayer && videoObj && videoObj.play && subtitles) {
			videoObj.play(1);
      if (isFireHbb) {
        videoObj.currentTime = 0;
        setAllTimouts();
      }
		}
	}

	$(document).ready(function() {

		var firAttrs = ['firetv-fullscreen',
			'firetv-tvimage-display',
			'firetv-margin-display',
			'firetv-scaling',
			'firetv-tv-format',
			'firetv-class',
			'firetv-profile'
		];
		for (var i = 0; i < firAttrs.length; i++) {
			if ($('html').attr(firAttrs[i])) {
				isFireHbb = true;
				break;
			}
		}

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

    initSubtitleMenu();

	});
})(jQuery);
