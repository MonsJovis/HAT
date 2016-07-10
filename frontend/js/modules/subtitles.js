(function($) {

	'use strict';

	var settings = {
		'language': null,
		'font-size': null,
		'font-color': null,
		'background-color': null,
		'position': null
	};

	var $videoPlayer,
		videoObj,
		$subtitleItem,
		subtitles,
		subtitleFiles = [],
		timeouts = [],
		isFireHbb = false;

	$(document).ready(function() {

		$videoPlayer = $('#videoplayer');
		$subtitleItem = $('.subtitle-item');
		if (!$videoPlayer.size()) return;

		var firAttrs = [
			'firetv-fullscreen',
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

		parseSubitleUrls();
		applySubtitleSettings();
		initSubtitleMenu();
		setTimeout(initVideoPlayer, 10);

	});

	function loadSubtitleFile(url, callback) {
		$.get(url, function(data) {
			subtitles = parseSubtitles(data);
			if (callback) callback();
		}, 'text');
	}

	function parseSubitleUrls() {
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
	}


	function initSubtitleMenu() {
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
			if ($mainMenu.css('display') !== 'none') {
				$('.focus', $mainMenu).removeClass('focus');
				$('[data-setting]', $mainMenu).first().addClass('focus');
			}
		}

		function onKeyDown() {
			var $menu = getOpenMenuObj();
			if (!$menu) return;
			var $focusedElement = $('.focus', $menu);
			if ($focusedElement.next().length) {
				$focusedElement.removeClass('focus').next().addClass('focus');
			}
		}

		function onKeyUp() {
			var $menu = getOpenMenuObj();
			if (!$menu) return;
			var $focusedElement = $('.focus', $menu);
			if ($focusedElement.prev().length) {
				$focusedElement.removeClass('focus').prev().addClass('focus');
			}
		}

		function onKeyRight() {
			var $menu = getOpenMenuObj();
			if (!$menu) return;
			if ($menu.hasClass('subtitle-main-menu')) {
				var $selectedLi = $('.focus', $menu),
					setting = $selectedLi.attr('data-setting'),
					$submenu = $('.subtitle-submenu-' + setting);
				$menu.hide();
				$('.focus', $menu).removeClass('focus');
				$('.focus', $submenu).removeClass('focus');
				$submenu.show().find('li').first().next().addClass('focus');
			}
		}

		function onKeyLeft() {
			var $menu = getOpenMenuObj();
			if (!$menu) return;
			if ($menu.hasClass('subtitle-submenu')) {
				$menu.hide();
				$('.subtitle-main-menu').show();
				var setting = $menu.attr('data-setting');
				if (setting) {
					$('.subtitle-main-menu [data-setting="' + setting + '"]').first().addClass('focus');
				}
			}
		}

		function onKeyEnter() {
			var $menu = getOpenMenuObj();
			if (!$menu) return;
			if ($menu.hasClass('subtitle-main-menu')) {
				onKeyRight();
			} else {
				var $selectedLi = $('.focus', $menu),
					settingValue = $selectedLi.attr('data-value');
				if (settingValue) {
					$selectedLi.parent().find('.enabled').removeClass('enabled');
					$selectedLi.addClass('enabled');
					applySubtitleSettings();
				} else {
					onKeyLeft();
				}
			}
		}

	}

	function applySubtitleSettings() {

		var languageBeforeSave = settings.language;
		$.each(settings, function(setting, index) {
			settings[setting] = $('.subtitle-submenu-' + setting + ' .enabled').attr('data-value');
		});

		if (settings.language !== languageBeforeSave) {
			var url;
			$.each(subtitleFiles, function(key, subtitleFile) {
				if (subtitleFile.language === settings.language) {
					url = subtitleFile.url;
				}
			});
			$subtitleItem.hide();
			clearAllTimeouts();
			loadSubtitleFile(url, function() {
				if (languageBeforeSave === null) {
					readyToPlayVideo();
				} else {
					setAllTimeouts();
				}
			});
		}

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

	function initVideoPlayer() {

		if ($videoPlayer.length) {
			videoObj = $('#videoplayer')[0];
			videoObj.onPlayStateChange = onPlayStateChange;
		}

		if (isFireHbb) {
			$videoPlayer.on('play', function() {
				setAllTimeouts();
			});
			$videoPlayer.on('pause', function() {
				setAllTimeouts();
			});
			$videoPlayer.on('ended', function() {
				setAllTimeouts();
			});
			$videoPlayer.on('seeked', function() {
				clearAllTimeouts();
				setAllTimeouts();
			});
		} else if (videoObj) {
			videoObj.onPlayStateChange = onPlayStateChange;
		}

		readyToPlayVideo();

	}

	function onPlayStateChange() { // unsupported :-(state, error) {
		try {
			debug('state: ' + denotePlaystate(videoObj.playState, videoObj.error) + ' at: ' + (videoObj.playPosition | 0) + '/' + (videoObj.playTime | 0));
			switch (videoObj.playState) {

				case 1: // PLAYING
					setAllTimeouts();
					break;

				case 2: // PAUSED
					clearAllTimeouts();
					break;

				case 4: // BUFFERING
					clearAllTimeouts();
					break;

				case 3: // CONNECTING
					break;

				case 5: // FINISHED
					break;

				case 0: // STOPPED
					clearAllTimeouts();
					break;

				case 6: // ERROR
					break;
			}

		} catch (ex) {
			debug('Video::onPlayStateChange caught: ' + ex);
		}
	}

	function setSubtitleTimeout(subtitle) {
		var currentTime = !isFireHbb ? videoObj.playPosition : videoObj.currentTime * 1000,
			showttl = subtitle.begin - currentTime,
			hidettl = subtitle.end - currentTime;
		if (hidettl < 0) {
			return;
		} else if (showttl < 0 && hidettl > 0) {
			showttl = 0;
		}
		if (!isNaN(showttl)) {
			timeouts.push(setTimeout(function() {
				$subtitleItem.data('subtitle', subtitle.id);
				$subtitleItem.html(subtitle.text).show();
			}, showttl));
		}
		if (!isNaN(hidettl)) {
			timeouts.push(setTimeout(function() {
				if ($subtitleItem.data('subtitle') === subtitle.id) {
					$subtitleItem.hide();
				}
			}, hidettl));
		}
	}

	function setAllTimeouts() {
		if (!subtitles) return;
		for (var i = 0; i < subtitles.length; i++) {
			setSubtitleTimeout(subtitles[i]);
		}
	}

	function clearAllTimeouts() {
		while (timeouts.length) {
			clearTimeout(timeouts.pop());
		}
	}

	function readyToPlayVideo() {
		if ($videoPlayer && videoObj && videoObj.play && typeof(subtitles) === 'object') {
			videoObj.play(1);
			if (isFireHbb) {
				videoObj.currentTime = 0;
				setAllTimeouts();
			}
		}
	}

	function parseSubtitles(data) {
		var subtitles = [], times;
		var lines = data.split("\n");
		var j = 1; //phrases counter
		var phrase = {
			id: j,
			begin: null,
			end: null,
			text: null
		};
		for (var i = 2; i < lines.length; i++) { //lines counter
			if (parseInt(lines[i], 10) == j) { //start new phrase
				continue;
			} else if (/\d\d:\d\d:\d\d\.\d\d\d.+/.test(lines[i])) { //push begin and end
				lines[i] = lines[i].slice(0, 29);
				times = lines[i].split(" --> ");
				phrase.begin = toMilliseconds(times[0]);
				phrase.end = toMilliseconds(times[1]);
			} else if (/\d\d:\d\d\.\d\d\d.+/.test(lines[i])) {
				lines[i] = lines[i].slice(0, 23);
				times = lines[i].split(" --> ");
				phrase.begin = toMilliseconds(times[0]);
				phrase.end = toMilliseconds(times[1]);
			} else if ((lines[i] === "\r") || (lines[i] === "\n") || (lines[i] === "")) { //push phrase to subtitles
				subtitles.push(phrase);
				j = j + 1;
				phrase = {
					id: j,
					begin: null,
					end: null,
					text: null
				};
			} else { //push text
				if (phrase.text === null) {
					phrase.text = lines[i];
				} else {
					phrase.text = phrase.text + "\n" + lines[i];
				}
			}
		}
		return subtitles;
	}

	function toMilliseconds(time) {
		var firstsplit, secondsplit;
		if (/\d\d:\d\d:\d\d\.\d\d\d/.test(time)) {
			firstsplit = time.split(":");
			secondsplit = firstsplit[2].split(".");
			return parseInt(firstsplit[0], 10) * 3600000 + parseInt(firstsplit[1], 10) * 60000 + parseInt(secondsplit[0], 10) * 1000 + parseInt(secondsplit[1], 10);
		} else if (/\d\d:\d\d\.\d\d\d/.test(time)) {
			firstsplit = time.split(':');
			secondsplit = firstsplit[1].split('.');
			return parseInt(firstsplit[0], 10) * 60000 + parseInt(secondsplit[0], 10) * 1000 + parseInt(secondsplit[1], 10);
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

	function debug(text) {
		if (!jQuery) return;
		var $debug = jQuery("#debugarea");
		if (!$debug.length) return;
		$debug
			.append(text + "\n")
			.scrollTop($debug[0].scrollHeight);
	}

})(jQuery);
