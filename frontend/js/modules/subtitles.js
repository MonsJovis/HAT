function debug(text) {
	var $debug = jQuery("#debugarea");
	if (!$debug.length) return;
	$debug
		.append(text + "\n")
		.scrollTop($debug[0].scrollHeight);
}

function parseSubtitles(data) {
	if (!data) {jQuery("#debugarea").append("there's is no data");}
	var subtitles = [];
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
			debug(lines[i]);
			continue;
		} else if (/\d\d:\d\d:\d\d\.\d\d\d.+/.test(lines[i])) { //push begin and end
			debug(lines[i]);
			lines[i] = lines[i].slice(0, 29);
			times = lines[i].split(" --> ");
			phrase.begin = toMilliseconds(times[0]);
			phrase.end = toMilliseconds(times[1]);
		} else if (/\d\d:\d\d\.\d\d\d.+/.test(lines[i])){
			debug(lines[i]);
			lines[i] = lines[i].slice(0, 23);
			times = lines[i].split(" --> ");
			phrase.begin = toMilliseconds(times[0]);
			phrase.end = toMilliseconds(times[1]);
		} else if ((lines[i] === "\r") || (lines[i] === "\n") || (lines[i] === "")) { //push phrase to subtitles
			debug(lines[i]);
			subtitles.push(phrase);
			j = j + 1;
			phrase = {
				id: j,
				begin: null,
				end: null,
				text: null
			};
		} else { //push text
			debug(lines[i]);
			if (phrase.text === null) {
				phrase.text = lines[i];
			} else {
				phrase.text = phrase.text + "\n" + lines[i];
			}
		}
	}
	if (!subtitles) {jQuery("#debugarea").append("there're no subtitles");}
	return subtitles;
}

function toMilliseconds(time) {
	if (/\d\d:\d\d:\d\d\.\d\d\d/.test(time)) {
		var firstsplit = time.split(":");
		var secondsplit = firstsplit[2].split(".");
		return parseInt(firstsplit[0], 10) * 3600000 + parseInt(firstsplit[1], 10) * 60000 + parseInt(secondsplit[0], 10) * 1000 + parseInt(secondsplit[1], 10);
	} else if (/\d\d:\d\d\.\d\d\d/.test(time)) {
		var firstsplit = time.split(":");
		var secondsplit = firstsplit[1].split(".");
		return parseInt(firstsplit[0], 10) * 60000 + parseInt(secondsplit[0], 10) * 1000 + parseInt(secondsplit[1], 10);
	}
}
