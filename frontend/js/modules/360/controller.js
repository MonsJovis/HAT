/*
*	Controller.js
*	Created by fst, 28.08.2015
*/

var Controller360 = function() {
	var $ = jQuery;
	var self = this;

	var video_id;

    var video_url;
    var ctrl_base_url;

    var video;

    var CTRL_SET_Y	= 0;
    var CTRL_SET_Z	= 1;

    var CTRL_OFFSET_Y	= 30;
    var CTRL_OFFSET_Z	= 31;

    var CTRL_MOV_Y	= 2;
    var CTRL_MOV_Z	= 3;

    var CTRL_ROT_Y	= 4;
    var CTRL_ROT_Z	= 5;

    var CTRL_ZOOM_TO = 6;
    var CTRL_ZOOM_BY = 7;

    var CTRL_FLW_SEL = 8;
    // var CTRL_FLW_TGL = 9;

    var CTRL_REC_START	= 10;
    var CTRL_REC_END	= 11;
    var CTRL_REC_SAVE	= 12;

    var CTRL_PLAY		= 20;
    var CTRL_PAUSE		= 21;

		var CTRL_SYNC		= 90;

		// cycle videos
    var next_video = 0;

    this.init = function() {
			log("Controller::init");

    	video = new Video360("container-video-360", "video360", 250);

    	return this;
    }

		// sync playout to server
		var initial_sync = 3;	// wait for first play
		var last_play_pos = -1;
		var pre_play_pos;
		var measured_delay = 0;

		var sync_timer = 0;

    // play video by NLV-UID
    function play(/*uid*/ cfg) {
		log("Controller::play uid: "+cfg.id);
		log("Controller::play fps: "+cfg.fps+" quality: "+cfg.quality);

        // request 360-projection (video- & ctrl-url)
        log("launch: "+LAUNCH_SERVER_URL);
        $.getJSON(LAUNCH_SERVER_URL,
        		{
            		uid: cfg.id,
								fps: cfg.fps,
								quality: cfg.quality
        		}
       	)
        .done(function(json) {
            log("launch - done: "+JSON.stringify(json));

            video_id = json.uid;

            //updateMeta(video_id, { playstate : "playing", playpos : { frame: 0 } } );

            video_url = json.video;
            ctrl_base_url = json.control;

						// test-mp4
						//video_url = "http://193.174.152.248/interop/video/Sintel.mp4";

						window.setTimeout(function() {
            	video.play( video_url,
                	{
                		prepare : function() {
											sync(1024*1024);
											try { clearInterval(sync_timer); } catch(ex) {}
										},
										buffer : function() {
											sync(1024*1024);
											try { clearInterval(sync_timer); } catch(ex) {}
										},
                		play : function() {
											log("Controller::Video::play");
                			ctrl(CTRL_ZOOM_BY, 1);

											sync();
											try { clearInterval(sync_timer); } catch(ex) {}
											sync_timer = window.setInterval(sync, 2500);

											/*
											if( 3 === initial_sync) {
												pre_play_pos = $('#video360')[0].playPosition | 0;
												log("3: pausing playout at time: "+pre_play_pos);
												initial_sync = 2;
												setTimeout(function() { ctrl(CTRL_PAUSE); }, 1);
											}
											*/
                		},
                		progress: function(pos, playtime, playState) {
											$('#playState').text(playState);
											$('#playPosition').text(''+pos+'/'+playtime);

											if(2 === initial_sync) {
												// playout from buffer
												if(pos == last_play_pos) {
													// buffer empty
													measured_delay = pos - pre_play_pos;
													log("2: buffer empty at time: "+pos+", measured buffer time: "+measured_delay);
													initial_sync = 1;
													setTimeout(function() { ctrl(CTRL_PLAY); }, 1);
												}
											}

											if(1 === initial_sync) {
												// re-buffering
												if(pos != last_play_pos) {
													// playout from buffer
													log("1: starve buffer - pause for time: "+(measured_delay-1000));
													// starve buffer
													initial_sync = 0;
													setTimeout(function() { ctrl(CTRL_PAUSE); }, 1);
													setTimeout(function() { ctrl(CTRL_PLAY); }, (measured_delay-1000));
												}
											}

											last_play_pos = pos;
										},
                		error : null,
                		complete: function() {
											log("Controller::Video::complete");
											try { window.clearInterval(sync_timer); } catch(ex) {}
                			play(cfg);	// re-start video
                		}
                	},
                	"video/mpeg");
								}, 200);
        })
        .fail(function(jqxhr, status, error) {
            log("launch - fail: "+status+" / "+$(error).html());
            log("statusCode: "+jqxhr.statusCode());
        });
    };
    this.play = play;

		this.starveBuffer = function() {
			// sync playout to server
			initial_sync = 3;	// wait for first play
			last_play_pos = -1;
			pre_play_pos = $('#video360')[0].playPosition | 0;
			measured_delay = 0;

			initial_sync = 2;
			setTimeout(function() { ctrl(CTRL_PAUSE); }, 1);
		};

		// Synchronize playout head to client playtime
		function sync(position_overwrite) {
			log('Controller::sync');

			var playpos = 'number' == typeof(position_overwrite) ?
				position_overwrite :
				$('#video360')[0].playPosition | 0;
			setTimeout(function() { ctrl(CTRL_SYNC, playpos); }, 1);
		};

    this.handleKey = function(key,cb) {
        log('Controller::handleKey: '+key);


        function ctrl_cb(json){
            cb(json);
        }

        switch(key) {

		case KeyEvent.VK_LEFT:
			ctrl(CTRL_ROT_Z, 25, ctrl_cb);
			//fade_arrow('#'+$('#arr_l').css('opacity', 1)[0].id);
			break;

		case KeyEvent.VK_RIGHT:
			ctrl(CTRL_ROT_Z, -25, ctrl_cb);
			//fade_arrow('#'+$('#arr_r').css('opacity', 1)[0].id);
			break;

		case KeyEvent.VK_UP:
			ctrl(CTRL_ROT_Y, -10, ctrl_cb);
			//fade_arrow('#'+$('#arr_u').css('opacity', 1)[0].id);
			break;

		case KeyEvent.VK_DOWN:
			ctrl(CTRL_ROT_Y, 10, ctrl_cb);
			//fade_arrow('#'+$('#arr_d').css('opacity', 1)[0].id);
			break;

		case KeyEvent.VK_7:
			ctrl(CTRL_ZOOM_BY, 1.1, ctrl_cb);
			break;

		case KeyEvent.VK_9:
			ctrl(CTRL_ZOOM_BY, 0.9, ctrl_cb);
			break;

		case KeyEvent.VK_0:
			ctrl(CTRL_SET_Y, 20);
			ctrl(CTRL_SET_Z, 0);
			ctrl(CTRL_ZOOM_TO, 60, ctrl_cb);
			break;

		case KeyEvent.VK_8:
			ctrl(CTRL_PAUSE, null, ctrl_cb);
			break;

		case KeyEvent.VK_5:
			ctrl(CTRL_PLAY, null, ctrl_cb);
			break;

		case KeyEvent.VK_1:
			ctrl(CTRL_FLW_SEL, (key - KeyEvent.VK_1), ctrl_cb);
			break;

		case KeyEvent.VK_2:
			self.starveBuffer();
			break;

		case KeyEvent.VK_3:
			$('body').toggleClass('terrax');
			break;

		case KeyEvent.VK_4:
			play(videos[++next_video%videos.length]);
			break;

		case KeyEvent.VK_6:
			DEBUG && $('#container-video').toggleClass('big');
			break;

		case KeyEvent.VK_GREEN:
			break;

		case KeyEvent.VK_YELLOW:
		case KeyEvent.VK_YELLOW1:
			$('body').toggleClass("fullscreen");
			break;

		case KeyEvent.VK_RED:
			break;
        default:
        	// not handle other keys..
          return  false;
        }
        // key handled
    }

    var streamer_state = {};
    function ctrl(code, value, cb) {
    	log("ctrl: "+code+", "+value);

    	// pass control-code and opt. value
        var url = ctrl_base_url+"/"+code + (value ? "/"+value : "");
		$.getJSON(url)
        .done(function(json) {
            log("ctrl - done");
            streamer_state = json;
			log("STATE: "+JSON.stringify(json));

						/*
            notifyMeta({
            	srv_state : json
            });
						*/

            cb && cb(json);
        })
        .fail(function() {
            log("ctrl - fail");
        });
    }

    var faders = {};
    function fade_arrow(sel_arrow) {

        var opacity = parseFloat($(sel_arrow).css('opacity') || "0");

        opacity -= 0.025;

        $(sel_arrow).css('opacity', opacity);

        try { faders[sel_arrow] && clearTimeout(faders[sel_arrow]); } catch(ex) {} // drop errors
        if(opacity > 0)
        	faders[sel_arrow] = setTimeout(function() { fade_arrow(sel_arrow); }, 50);
	}
};
