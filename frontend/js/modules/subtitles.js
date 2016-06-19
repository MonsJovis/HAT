function parseSubtitles(data) {
    var subtitles = [];
	var lines = data.split("\n");
	var j = 1; //count phrases
	var phrase = {
		begin: "",
		end: "",
		text: ""
	};
	for (var i=2; i<lines.length; i++){ //count lines
		if (parseInt(lines[i])==j){ //start new phrase
			continue;
		}
		else if (/\d\d:\d\d:\d\d\.\d\d\d.+/.test(lines[i])) { //push begin and end
			lines[i] = lines[i].slice(0, 28);
			times = lines[i].split(" --> ");
			phrase.begin = toMilliseconds(times[0]);
			phrase.end = toMilliseconds(times[1]);
			continue;
		}
		else if ((lines[i] == "\r") || (lines[i] == "\n") || (lines[i] == "") ){ //push phrase to subtitles
			subtitles.push(phrase);
			j = j+1;
			phrase = {
				begin: "",
				end: "",
				text: ""
			};
			continue;
		}
		else { //push text
	 		if (phrase.text == ""){
				phrase.text = lines[i];
			}
			else
			{
				phrase.text = phrase.text + "\n" + lines[i];

			}
			continue;
		}
	}
return subtitles;
}
function toMilliseconds(time){
    var firstsplit = time.split(":");
    var secondsplit = firstsplit[2].split(".");
    return parseInt(firstsplit[0]) * 3600000 + parseInt(firstsplit[1]) * 60000 + parseInt(secondsplit[0]) * 1000 + parseInt(secondsplit[1]);
}
