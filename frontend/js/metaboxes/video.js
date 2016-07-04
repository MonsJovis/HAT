(function($) {

	function debug(text) {
		$("#debugarea")
			.append(text + "\n")
			.scrollTop($("#debugarea")[0].scrollHeight);
	}

	var settings = {
		'language': null,
		'font-size': null,
		'font-color': null,
		'background-color': null,
		'position': null
	};

	function initSubtitleMenu() {

		generateSubtitleStyles();

		$(document).keydown(function(e) {
			switch (e.keyCode) {
				case VK_RIGHT:
					e.preventDefault();
					onKeyRight();
					break;
				case VK_LEFT:
					e.preventDefault();
					onKeyLeft();
					break;
				case VK_DOWN:
					e.preventDefault();
					onKeyDown();
					break;
				case VK_UP:
					e.preventDefault();
					onKeyUp();
					break;
				case VK_ENTER:
					e.preventDefault();
					onKeyEnter();
					break;
				case VK_YELLOW:
					e.preventDefault();
					toggleMenu();
					break;
			}
		});

		function getOpenMenuObj() {
			var $menu = null;
			$('.subtitle-menu').each(function() {
				if ($(this).css('display') !== 'none') {
					$menu = $(this);
				}
			});
			return $menu;
		}

		function toggleMenu() {
			var $mainMenu = $('.subtitle-main-menu');
			$mainMenu.toggle();
			$('.subtitle-submenu').hide();
			if ($mainMenu.css('display') != 'none') {
				$('[data-setting]', $mainMenu).first().find('a').focus();
			}
		}

		function onKeyDown() {
			var $menu = getOpenMenuObj();
			if (!$menu) return;
			$(':focus', $menu).parent().next().find('a').focus();
		}

		function onKeyUp() {
			var $menu = getOpenMenuObj();
			if (!$menu) return;
			$(':focus', $menu).parent().prev().find('a').focus();
		}

		function onKeyRight() {
			var $menu = getOpenMenuObj();
			if (!$menu) return;
			if ($menu.hasClass('subtitle-main-menu')) {
				var $selectedLi = $(':focus', $menu).parent(),
					setting = $selectedLi.attr('data-setting');
				$menu.hide();
				$('.subtitle-submenu-' + setting).show().find('li').first().next().find('a').focus();
			}
		}

		function onKeyLeft() {
			var $menu = getOpenMenuObj();
			if (!$menu) return;
			if ($menu.hasClass('subtitle-submenu')) {
				$menu.hide();
				var setting = $menu.attr('data-setting');
				$('.subtitle-main-menu').show();
				$('.subtitle-main-menu [data-setting="' + setting + '"]').first().find('a').focus();
			}
		}

		function onKeyEnter() {
			var $menu = getOpenMenuObj();
			if (!$menu) return;
			var $selectedLi = $(':focus', $menu).parent();
			if ($menu.hasClass('subtitle-main-menu')) {
				onKeyLeft();
			} else {
				var settingValue = $selectedLi.attr('data-value');
				if (settingValue === undefined) return;
				$selectedLi.parent().find('.enabled').removeClass('enabled');
				$selectedLi.addClass('enabled');
				generateSubtitleStyles();
			}
		}

		$('.subtitle-menu-btn').click(toggleMenu);

		$('.subtitle-main-menu li').click(function() {
			var setting = $(this).attr('data-setting');
			$('.subtitle-main-menu').hide();
			$('.subtitle-submenu-' + setting).show();
		});

		$('.subtitle-submenu .back').click(function() {
			var setting = $(this).attr('data-setting');
			$('.subtitle-submenu').hide();
			$('.subtitle-main-menu').show();
		});

		$('.subtitle-submenu li').click(function() {
			var settingValue = $(this).attr('data-value');
			if (settingValue === undefined) return;
			$(this).parent().find('.enabled').removeClass('enabled');
			$(this).addClass('enabled');
			setTimeout(generateSubtitleStyles, 100);
		});

	}

	function generateSubtitleStyles() {

		$.each(settings, function(setting, index) {
			settings[setting] = $('.subtitle-submenu-' + setting + ' .enabled').attr('data-value');
		});
		var styles = {
			color: settings['font-color'],
			backgroundColor: settings['background-color'],
			fontSize: null,
			bottom: settings.position === 'bottom' ? '60px' : 'auto',
			top: settings.position === 'top' ? '60px' : 'auto',
		};
		switch (settings['font-size']) {
			case 'small':
				styles.fontSize = '1em';
				break;
			case 'medium':
				styles.fontSize = '1.3em';
				break;
			case 'big':
				styles.fontSize = '1.5em';
				break;
		}
		$('.subtitle-item').css(styles);
	}

	var $videoPlayer,
		videoObj,
		$subtitleItem,
		subtitles,
		subtitleFiles = [],
		timeouts = [],
		isFireHbb = false;

	function initVideoPlayer() {

		debug("initVideoPlayer");

		$videoPlayer = $('#videoplayer');
		$subtitleItem = $('.subtitle-item');

		if ($videoPlayer.length) {
			videoObj = $('#videoplayer')[0];
			videoObj.onPlayStateChange = onPlayStateChange;
		}

		if (isFireHbb) {
			$videoPlayer.on('play', function() {
				debug("play");
				setAllTimeouts();
			});
			$videoPlayer.on('pause', function() {
				debug("pause");
				clearAllTimeou();
			});
			$videoPlayer.on('ended', function() {
				log('ended');
			});
			$videoPlayer.on('seeked', function() {
				log('seeked');
				clearAllTimeouts();
				setAllTimeouts();
			});
		} else if (videoObj) {
			videoObj.onPlayStateChange = onPlayStateChange;
		}

		function onPlayStateChange() { // unsupported :-(state, error) {
			try {
				debug('state: ' + denotePlaystate(videoObj.playState, videoObj.error) + ' at: ' + (videoObj.playPosition | 0) + '/' + (videoObj.playTime | 0));
				switch (videoObj.playState) {

					case 1: // PLAYING
						debug('onPlayStateChange: playing');
						setAllTimeouts();
						break;

					case 2: // PAUSED
						debug('onPlayStateChange: paused');
						clearAllTimeouts();
						break;

					case 4: // BUFFERING
						debug('onPlayStateChange: buffering');
						clearAllTimeouts();
						break;

					case 3: // CONNECTING
						debug('onPlayStateChange: connecting');
						break;

					case 5: // FINISHED
						debug('onPlayStateChange: finished');
						break;

					case 0: // STOPPED
						debug('onPlayStateChange: stopped');
						clearAllTimeouts();
						break;

					case 6: // ERROR
						debug('onPlayStateChange: error');
						break;
				}

			} catch (ex) {
				debug('Video::onPlayStateChange caught: ' + ex);
			}
		}


		readyCallback();

	}

	function setSubtitleTimeout(subtitle) {
		var currentTime = videoObj.currentTime || videoObj.playPosition,
			showttl = subtitle.begin - currentTime * 1000,
			hidettl = subtitle.end - currentTime * 1000;
		if (hidettl < 0) {
			return;
		} else if (showttl < 0 && hidettl > 0) {
			showttl = 0;
		}
		timeouts.push(setTimeout(function() {
			debug(showttl + ': ' + subtitle.id + ' (' + currentTime + ')');
			$subtitleItem.data('subtitle', subtitle.id);
			$subtitleItem.html(subtitle.text).show();
		}, showttl));
		timeouts.push(setTimeout(function() {
			if ($subtitleItem.data('subtitle') === subtitle.id) {
				$subtitleItem.hide();
			}
		}, hidettl));
	}

	function setAllTimeouts() {
		if (!subtitles) return;
		debug("setAllTimeouts: " + subtitles.length + " subtitles");
		for (var i = 0; i < subtitles.length; i++) {
			setSubtitleTimeout(subtitles[i]);
		}
	}

	function clearAllTimeouts() {
		debug("clearAllTimeouts");
		while (timeouts.length) {
			clearTimeout(timeouts.pop());
		}
	}

	function readyCallback() {
		if ($videoPlayer && videoObj && videoObj.play && subtitles !== null) {
			videoObj.play(1);
			if (isFireHbb) {
				videoObj.currentTime = 0;
				setAllTimeouts();
			}
		}
	}

	function denotePlaystate(state, error) {
		switch (state) {
			case 0:
				return "STOPPED";
			case 1:
				return "PLAYING";
			case 2:
				return "PAUSED";
			case 3:
				return "CONNECTING";
			case 4:
				return "BUFFERING";
			case 5:
				return "FINISHED";
			case 6:
				return "ERROR [" + denoteVideoError(error) + "]";
			default:
				return "Unexpected state code: " + state;
		}
	}

	function denoteVideoError(error) {

		switch (error) {
			case 0:
				return "A/V FORMAT NOT SUPPORTED";
			case 1:
				return "CANNOT CONNECT TO SERVER OR CONNECTION LOST";
			case 2:
				return "UNIDENTIFIED ERROR";
				// by DAE 1.1 p. 263:
			case 3:
				return "INSUFFICIENT RESOURCES";
			case 4:
				return "CONTENT CORRUPT OR INVALID";
			case 5:
				return "CONTENT NOT AVAILABLE";
			case 6:
				return "CONTENT NOT AVAILABLE AT A GIVEN POSITION";
				// (by ETSI 1.2.1)
			case 7:
				return "CONTENT BLOCKED DUE TO PARENTAL CONTROL";
			default:
				return "Unexpected error code: " + error;
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
					debug("ajax: success");
					subtitles = parseSubtitles(data);
					readyCallback();
				}, 'text').fail(function() {
					debug("ajax: error");
				})
				.always(function() {
					debug("ajax: finished");
				});
		}

		setTimeout(initVideoPlayer, 10);

		initSubtitleMenu();

	});
})(jQuery);
