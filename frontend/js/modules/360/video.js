/**
 * Video PLayer
 */

var Video360 = function(container_id, video_id, progress_interval) {

	var $=jQuery;
	var PLAYPOS_UPDATE_INTERVAL = progress_interval || 1000;

	var _events = null;;
	var _progress = null;
	var _video_id = "#"+video_id;
	var _active = false;	// set on play(); reset on complete()

	this.play = function(url, events, mime) {
		try {
			log('Video::play url: '+url);
			reset();
			_active = true;		// TRUE until completed
			_events = events || {};

			$('#'+container_id).empty().append(
				$('<object>', {
					id: video_id,
					// 'class': 'fullscreen',
					'style': 'position: absolute; width: 100%; height: 100%',
					type: mime || 'video/mpeg'
				})
			);

			var $video = $(_video_id);

			$video[0].onPlayStateChange = onPlayStateChange;

			$video.prop('data', url);
			$video[0].play && $video[0].play(1);
			window.setTimeout(updateProgress, 1);
			_progress = window.setInterval(updateProgress, PLAYPOS_UPDATE_INTERVAL);


		} catch(ex) {
			log('Video::play caught: ', dumpex(ex));
			error();
		}
	};

	this.stop = function() {
		reset();
	};

	this.getPlayPosition = function() {

		return $(_video_id)[0].playPosition;
	};

	function reset() {
		log('Video::reset');
		try {
			var videoObj = $(_video_id);
			log(' ');
			if(videoObj.length) {
				var video = $(_video_id)[0];
				video.onPlayStateChange = null;
				video.stop();
			}
			videoObj.remove();
		} catch(ex) {
			log('Video::reset (handler, stop) caught: ', dumpex(ex));
		}
		try {
			log(' ');
			window.clearInterval(_progress);
		} catch(ex) {
			log('Video::reset (clear interval) caught: ', dumpex(ex));
		}
		_progress = null;
	}

	function buffer() {
		log('Video::buffer');
		if(!_active)	// already completed?
			return;

		try {
			_events.buffer && window.setTimeout(_events.buffer, 1);
		} catch(ex) {
			log('Video::buffer user code exception: ', dumpex(ex));
		}
	}

	function prepare() {
		log('Video::prepare');
		if(!_active)	// already completed?
			return;

		try {
			_events.prepare && window.setTimeout(_events.prepare, 1);
		} catch(ex) {
			log('Video::prepare user code exception: ', dumpex(ex));
		}
	}

	function play() {
		log('Video::play');
		if(!_active)	// already completed?
			return;

		try {
			_events.play && window.setTimeout(_events.play, 1);
		} catch(ex) {
			log('Video::play user code exception: ', dumpex(ex));
		}
	}

	function error() {
		log('Video::error');
		if(!_active)	// already completed?
			return;

		try {
			_events.error && window.setTimeout(_events.error, 1);
		} catch(ex) {
			log('Video::error user code exception: ', dumpex(ex));
		}
		//complete();
	}

	function complete() {
		log('Video::complete');

		if(!_active)	// already completed?
			return;
		_active = false;

		try {
			var $video = $(_video_id);
			$video.prop('data', null);
		} catch(ex) {
			log('Video::complete reset url threw: ', dumpex(ex));
		}

		try {
			_events.complete && window.setTimeout(_events.complete, 1);
		} catch(ex) {
			log('Video::complete user code exception: ', dumpex(ex));
		}
		reset();
	}

	function updateProgress(set_position) {
		// log('Video::updateProgress');

		if(!_active)	// already completed?
			return;

		var video = $(_video_id)[0];
		var pos = video.playPosition | 0; // strip fractions
		if( 0 == pos && 1 == video.playState) log('Video::updateProgress... still pos 0! '+denotePlaystate(video.playState, video.error));

		_events.progress && window.setTimeout(function() {_events.progress(set_position || pos, video.playTime, video.playState);}, 1);

		/*
		if(pos >= video.playTime && 1 == video.playState) {
			log('Video::updateProgress: generate STOP and end of video!');
			window.setTimeout(function() {
				video.stop();
			}, 1);
		}
		*/
	}

	function onPlayStateChange() { // unsupported :-(state, error) {
		log('Video::onPlayStateChange'); // state: '+state);

		try {
			var video = $(_video_id)[0];
			log('state: '+denotePlaystate(video.playState, video.error)+' at: '+(video.playPosition | 0)+'/'+(video.playTime | 0));

			switch(video.playState) {

			case 1: // PLAYING
				play();
				break;

			case 2: // PAUSED

			case 4: // BUFFERING
				buffer();
				updateProgress();
				// fall through
			case 3: // CONNECTING
				prepare();
				break;

			case 5: // FINISHED
				window.setTimeout(function() { video.stop(); }, 1);
				//break;

			case 0: // STOPPED
				updateProgress();
				complete();
				break;

			case 6: // ERROR
				error();
				break;
			}

		} catch(ex) {
			log('Video::onPlayStateChange caught: ', dumpex(ex));
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
			errorlog("VIDEO ERROR ["+denoteVideoError(error)+"]");
			return "ERROR ["+denoteVideoError(error)+"]";
		default:
			return "Unexpected state code: "+state;
		}
	};

	function denoteVideoError(error) {

		switch(error) {
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
			return "Unexpected error code: "+error;
		}
	}

};
