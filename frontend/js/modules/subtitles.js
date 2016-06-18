function parseSubtitles(data) {
    var subtitles = [];
    var phrases = data.split("\n\n");
    var lines = [];
    var times = [];
    for (var i=1; i<phrases.length; i++){
        var phrase = {
            begin: "",
            end: "",
            text: ""
        };
        lines = phrases[i].split("\n");
        times = lines[1].split(" --> ");
        phrase.begin = toMilliseconds(times[0]);
        phrase.end = toMilliseconds(times[1]);
        for (var j=2; j<lines.length; j++){ //counts lines of text
            if (phrase.text == ""){
                phrase.text = lines[j];
            }
            else
            {
                phrase.text = phrase.text + "\n" + lines[j];
            }
        }
        subtitles.push(phrase);
    }
    return subtitles;
}

function toMilliseconds(time){
    var firstsplit = time.split(":");
    var secondsplit = firstsplit[2].split(".");
    return parseInt(firstsplit[0]) * 3600000 + parseInt(firstsplit[1]) * 60000 + parseInt(secondsplit[0]) * 1000 + parseInt(secondsplit[1]);
}
